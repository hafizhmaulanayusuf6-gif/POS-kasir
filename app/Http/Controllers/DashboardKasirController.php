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

        // data omzet 7 hari terakhir untuk chart
        $omzetMingguan = Transaksi::selectRaw('DATE(created_at) as tanggal, SUM(total_bayar) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Susun untuk 7 hari terakhir, isi 0 kalau tidak ada transaksi hari itu
        $labelChart = [];
        $dataChart = [];
        for ($i = 6; $i >= 0; $i-- ) {
            $tanggal = now()->subDays($i)->format('Y-m-d');
            $labelChart[] = now()->subDays($i)->translatedFormat('d M');
            $cocok = $omzetMingguan->firstWhere('tanggal', $tanggal);
            $dataChart[] = $cocok ? (int) $cocok->total : 0;
        }

        return view('dashboard-kasir.index', compact(
            'omzetHariIni',
            'jumlahTransaksiHariIni',
            'produkTerlaris',
            'metodeBayar',
            'transaksiTerbaru',
            'labelChart',
            'dataChart'
        ));
    }
}
