<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $produks = Produk::with('kategori')->where('stok', '>', 0)->get();
        return view('kasir.index', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|json',
            'bayar' => 'required|numeric|min:0',
        ]);

        $cart = json_decode($request->cart, true);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        // Cek stok cukup untuk semua item SEBELUM menyimpan apapun
        foreach ($cart as $item) {
            $produk = Produk::find($item['id']);
            if (! $produk || $produk->stok < $item['jumlah']) {
                return back()->with('error', 'Stok produk "' . ($item['nama'] ?? '') . '" tidak mencukupi.');
            }
        }

        $totalBayar = collect($cart)->sum(fn ($item) => $item['harga'] * $item['jumlah']);

        if ($request->bayar < $totalBayar) {
            return back()->with('error', 'Uang bayar kurang dari total belanja.');
        }

        DB::transaction(function () use ($cart, $totalBayar, $request) {
            $kodeUrut = Transaksi::whereDate('created_at', today())->count() + 1;

            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . now()->format('Ymd') . '-' . str_pad($kodeUrut, 4, '0', STR_PAD_LEFT),
                'user_id' => Auth::id(),
                'total_bayar' => $totalBayar,
                'bayar' => $request->bayar,
                'kembalian' => $request->bayar - $totalBayar,
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