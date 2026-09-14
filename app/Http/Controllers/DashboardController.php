<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $totalProduk = Produk::count();
        $totalKategori = Kategori::count();
        $totalNilaiStok = Produk::all()->sum(function ($produk) {
            return $produk->harga * $produk->stok;
        });
        $totalStok = Produk::sum('stok');
        $produkStokMenipis = Produk::where('stok', '<', 5)->count();

        $produkPerKategori = Kategori::withCount('produks')->get();

        return view('dashboard', compact(
            'totalProduk',
            'totalKategori',
            'totalNilaiStok',
            'totalStok',
            'produkStokMenipis',
            'produkPerKategori'
        ));
    }
}
