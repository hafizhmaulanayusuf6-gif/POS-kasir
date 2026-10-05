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

        <div class="card mb-4">
            <div class="card-header">Omzet 7 Hari Terakhir</div>
            <div class="card-body">
                <canvas id="chartOmzet" height="80"></canvas>
            </div>
        </div>

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

{{-- ============ OMZET BULANAN (khusus admin) ============ --}}
@if (Auth::user()->isAdmin() && isset($dataBulanan))
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Omzet Bulanan {{ $tahunDipilih }}
            <span class="text-muted small ms-2">Total: Rp {{ number_format($totalOmzetTahun, 0, ',', '.') }}</span>
        </span>
        <form method="GET" action="{{ route('dashboard-kasir.index') }}">
            <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach ($daftarTahun as $th)
                <option value="{{ $th }}" @selected($th==$tahunDipilih)>{{ $th }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body">
        <canvas id="chartOmzetBulanan" height="80"></canvas>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('chartOmzet').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labelChart),
            datasets: [{
                label: 'Omzet (Rp)',
                data: @json($dataChart),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
            }]
        },

        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>

@if (Auth::user()->isAdmin() && isset($dataBulanan))
<script>
    new Chart(document.getElementById('chartOmzetBulanan').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($labelBulanan),
            datasets: [{
                label: 'Omzet (Rp)',
                data: @json($dataBulanan),
                backgroundColor: 'rgba(25, 135, 84, 0.6)',
                borderColor: '#198754',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endif
@endsection