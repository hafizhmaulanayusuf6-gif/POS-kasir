<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;

class DashboardKasirController extends Controller
{
    public function index()
    {
        $omzetHariIni = Transaksi::whereDate('created_at', today())->sum('total_bayar');
        $jumlahTransaksiHariIni = Transaksi::whereDate('created_at', today())->count();


        $produkTerlaris = TransaksiDetail::selectRaw('nama_produk, SUM(jumlah) as total_terjual')
            ->groupBy('nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $metodeBayar = Transaksi::selectRaw('metode_bayar, COUNT(*) as jumlah')
            ->whereDate('created_at', today())
            ->groupBy('metode_bayar')
            ->get();

        $transaksiTerbaru = Transaksi::with('user')->latest()->limit(5)->get();

        return view('dashboard-kasir.index', compact(
            'omzetHariIni',
            'jumlahTransaksiHariIni',
            'produkTerlaris',
            'metodeBayar',
            'transaksiTerbaru'
        ));
    }
}
