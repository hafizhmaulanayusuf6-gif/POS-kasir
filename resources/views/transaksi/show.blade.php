@extends('layouts.main')

@section('title', 'Detail Transaksi')

@section('content')

<h2>Detail Transaksi: {{ $transaksi->kode_transaksi }}</h2>

<a href="{{ route('transaksi.index') }}" class="btn btn-secondary mb-3">← Kembali</a>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-1"><strong>Tanggal:</strong> {{ $transaksi->created_at->format('d M Y, H:i') }}</p>
                <p class="mb-1"><strong>Kasir:</strong> {{ $transaksi->user->name ?? '-' }}</p>
                <p class="mb-1"><strong>Metode Bayar:</strong> {{ strtoupper($transaksi->metode_bayar) }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Total:</strong> Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</p>
                <p class="mb-1"><strong>Bayar:</strong> Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</p>
                <p class="mb-1"><strong>Kembalian:</strong> Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaksi->details as $d)
        <tr>
            <td>{{ $d->nama_produk }}</td>
            <td>Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
            <td>{{ $d->jumlah }}</td>
            <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection