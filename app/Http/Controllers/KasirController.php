<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Kategori;
use DomainException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $produks = Produk::with('kategori')->where('stok', '>', 0)->get();
        $kategoris = Kategori::all();
        return view('kasir.index', compact('produks', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|json',
            'metode_bayar' => 'required|in:cash,qris,transfer',
            'bayar' => 'nullable|numeric|min:0',
        ]);

        $cart = json_decode($request->cart, true);

        if (! is_array($cart) || empty($cart)) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        // Dari browser HANYA id produk dan jumlah yang dipakai.
        // Harga dan nama selalu diambil dari database (lihat prosesCheckout).
        // Baris dengan produk yang sama digabung agar tidak bisa melewati batas stok.
        $jumlahPerProduk = [];

        foreach ($cart as $item) {
            $id = is_array($item) ? filter_var($item['id'] ?? null, FILTER_VALIDATE_INT) : false;
            $jumlah = is_array($item) ? filter_var($item['jumlah'] ?? null, FILTER_VALIDATE_INT) : false;

            if ($id === false || $jumlah === false || $id < 1 || $jumlah < 1) {
                return back()->with('error', 'Data keranjang tidak valid.');
            }

            $jumlahPerProduk[$id] = ($jumlahPerProduk[$id] ?? 0) + $jumlah;
        }

        try {
            $transaksi = $this->prosesCheckout($jumlahPerProduk, $request->metode_bayar, $request->bayar);
        } catch (DomainException $e) {
            // Pesan error bisnis (stok kurang, uang bayar kurang, dll.)
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('kasir.index')->with(
            'success',
            'Transaksi ' . $transaksi->kode_transaksi . ' berhasil! Kembalian: Rp ' . number_format($transaksi->kembalian, 0, ',', '.')
        );
    }

    /**
     * Seluruh pengecekan dan penyimpanan dilakukan dalam SATU transaksi database.
     * Baris produk dikunci (lockForUpdate) agar dua kasir yang checkout produk
     * yang sama bersamaan tidak membuat stok menjadi negatif.
     *
     * @param  array<int,int>  $jumlahPerProduk  [produk_id => jumlah]
     * @throws DomainException jika ada pelanggaran aturan bisnis
     */
    private function prosesCheckout(array $jumlahPerProduk, string $metodeBayar, $bayarInput): Transaksi
    {
        // Kode transaksi bisa bentrok jika dua transaksi dibuat bersamaan.
        // Kolom kode_transaksi unique, jadi database menolak dan kita ulangi
        // (seluruh transaksi di-rollback, sehingga stok tidak terpotong dua kali).
        for ($percobaan = 1;; $percobaan++) {
            try {
                return DB::transaction(function () use ($jumlahPerProduk, $metodeBayar, $bayarInput) {
                    $produks = Produk::whereIn('id', array_keys($jumlahPerProduk))
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    // Validasi stok + hitung total memakai HARGA DARI DATABASE
                    $totalBayar = 0;

                    foreach ($jumlahPerProduk as $id => $jumlah) {
                        $produk = $produks->get($id);

                        if (! $produk) {
                            throw new DomainException('Produk tidak ditemukan.');
                        }

                        if ($produk->stok < $jumlah) {
                            throw new DomainException('Stok produk "' . $produk->nama_produk . '" tidak mencukupi.');
                        }

                        $totalBayar += $produk->harga * $jumlah;
                    }

                    // Cash: uang bayar harus cukup. QRIS/Transfer: dianggap dibayar pas.
                    if ($metodeBayar === 'cash') {
                        if (! $bayarInput || $bayarInput < $totalBayar) {
                            throw new DomainException('Uang bayar kurang dari total belanja.');
                        }
                        $bayar = $bayarInput;
                        $kembalian = $bayar - $totalBayar;
                    } else {
                        $bayar = $totalBayar;
                        $kembalian = 0;
                    }

                    // Format kode tetap TRX-YYYYMMDD-0001, tapi urutannya diambil dari
                    // kode terakhir hari ini (bukan count) agar tidak salah jika ada data terhapus.
                    $prefix = 'TRX-' . now()->format('Ymd') . '-';
                    $kodeTerakhir = Transaksi::where('kode_transaksi', 'like', $prefix . '%')->max('kode_transaksi');
                    $kodeUrut = $kodeTerakhir ? ((int) substr($kodeTerakhir, strlen($prefix))) + 1 : 1;

                    $transaksi = Transaksi::create([
                        'kode_transaksi' => $prefix . str_pad($kodeUrut, 4, '0', STR_PAD_LEFT),
                        'user_id' => Auth::id(),
                        'metode_bayar' => $metodeBayar,
                        'total_bayar' => $totalBayar,
                        'bayar' => $bayar,
                        'kembalian' => $kembalian,
                    ]);

                    foreach ($jumlahPerProduk as $id => $jumlah) {
                        $produk = $produks->get($id);

                        TransaksiDetail::create([
                            'transaksi_id' => $transaksi->id,
                            'produk_id' => $produk->id,
                            'nama_produk' => $produk->nama_produk,
                            'harga' => $produk->harga,
                            'jumlah' => $jumlah,
                            'subtotal' => $produk->harga * $jumlah,
                        ]);

                        $produk->decrement('stok', $jumlah);
                    }

                    return $transaksi;
                });
            } catch (UniqueConstraintViolationException $e) {
                if ($percobaan >= 3) {
                    throw $e;
                }
            }
        }
    }
}
