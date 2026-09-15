<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Kategori;
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

        if (empty($cart)) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        foreach ($cart as $item) {
            $produk = Produk::find($item['id']);
            if (! $produk || $produk->stok < $item['jumlah']) {
                return back()->with('error', 'Stok produk "' . ($item['nama'] ?? '') . '" tidak mencukupi.');
            }
        }

        $totalBayar = collect($cart)->sum(fn($item) => $item['harga'] * $item['jumlah']);

        // Untuk cash, uang bayar harus cukup. Untuk QRIS/Transfer, dianggap dibayar pas (tidak ada kembalian)
        if ($request->metode_bayar === 'cash') {
            if (! $request->bayar || $request->bayar < $totalBayar) {
                return back()->with('error', 'Uang bayar kurang dari total belanja.');
            }
            $bayar = $request->bayar;
            $kembalian = $bayar - $totalBayar;
        } else {
            $bayar = $totalBayar;
            $kembalian = 0;
        }

        DB::transaction(function () use ($cart, $totalBayar, $bayar, $kembalian, $request) {
            $kodeUrut = Transaksi::whereDate('created_at', today())->count() + 1;

            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . now()->format('Ymd') . '-' . str_pad($kodeUrut, 4, '0', STR_PAD_LEFT),
                'user_id' => Auth::id(),
                'metode_bayar' => $request->metode_bayar,
                'total_bayar' => $totalBayar,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
            ]);

            foreach ($cart as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['id'],
                    'nama_produk' => $item['nama'],
                    'harga' => $item['harga'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['harga'] * $item['jumlah'],
                ]);

                Produk::find($item['id'])->decrement('stok', $item['jumlah']);
            }
        });

        return redirect()->route('kasir.index')->with('success', 'Transaksi berhasil disimpan!');
    }
}
