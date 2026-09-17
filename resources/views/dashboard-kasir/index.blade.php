@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')

    <h2>Dashboard</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Omzet Hari Ini</div>
                    <div class="fs-4 fw-bold">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Transaksi Hari Ini</div>
                    <div class="fs-4 fw-bold">{{ $jumlahTransaksiHariIni }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Metode Bayar Hari Ini</div>
                    @forelse ($metodeBayar as $m)
                        <div class="small">{{ strtoupper($m->metode_bayar) }}: {{ $m->jumlah }}x</div>
                    @empty
                        <div class="small text-muted">Belum ada transaksi</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Produk Terlaris</div>
                <ul class="list-group list-group-flush">
                    @forelse ($produkTerlaris as $p)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $p->nama_produk }}</span>
                            <span class="badge bg-primary">{{ $p->total_terjual }} terjual</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Belum ada data.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Transaksi Terbaru</div>
                <ul class="list-group list-group-flush">
                    @forelse ($transaksiTerbaru as $t)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>
                                <a href="{{ route('transaksi.show', $t->id) }}">{{ $t->kode_transaksi }}</a>
                                <div class="small text-muted">{{ $t->user->name ?? '-' }}</div>
                            </span>
                            <span>Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Belum ada transaksi.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

@endsection