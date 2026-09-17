<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;

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
}
