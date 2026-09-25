<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Exports\TransaksiExport;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    public function index(Request $request) 
    {
        $transaksis = Transaksi::with('user')
            ->when($request->tanggal, function ($query) use ($request) {
                $query->whereDate('created_at', $request->tanggal);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('transaksi.index', compact('transaksis'));
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load('details', 'user');
        return view('transaksi.show', compact('transaksi'));
    }

    public function struk(Transaksi $transaksi)
    {
        $transaksi->load('details', 'user');
        return view('transaksi.struk', compact('transaksi'));
    }

    public function export(Request $request)
    {
        $nama = 'laporan-transaksi-' . now()->format('Ymd') . '.xlsx';
        return Excel::download(
            new TransaksiExport($request->tanggal_awal, $request->tanggal_akhir),
            $nama
        );
    }
}
