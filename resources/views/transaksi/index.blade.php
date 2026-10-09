
@extends('layouts.main')

@section('title', 'Riwayat Transaksi')

@section('content')

<h2>Riwayat Transaksi</h2>

@php
    $periode = request(
        'periode',
        request('tanggal') ? 'harian' : 'semua'
    );
@endphp

<form method="GET"
    action="{{ route('transaksi.index') }}"
    class="mb-3 d-flex flex-wrap align-items-center gap-2"
    id="form-filter">

    <select name="periode"
        id="periode"
        class="form-select"
        style="width: 170px;">

        <option value="semua"
            {{ $periode === 'semua' ? 'selected' : '' }}>
            Semua
        </option>

        <option value="harian"
            {{ $periode === 'harian' ? 'selected' : '' }}>
            Harian
        </option>

        <option value="mingguan"
            {{ $periode === 'mingguan' ? 'selected' : '' }}>
            Mingguan
        </option>

        <option value="bulanan"
            {{ $periode === 'bulanan' ? 'selected' : '' }}>
            Bulanan
        </option>

        <option value="rentang"
            {{ $periode === 'rentang' ? 'selected' : '' }}>
            Rentang Tanggal
        </option>
    </select>

    <input type="date"
        name="tanggal"
        id="tanggal"
        class="form-control filter-tanggal"
        style="width: 165px;"
        value="{{ request('tanggal', now()->toDateString()) }}">

    <input type="date"
        name="tanggal_awal"
        id="tanggal-awal"
        class="form-control filter-rentang"
        style="width: 155px;"
        value="{{ request('tanggal_awal') }}"
        aria-label="Tanggal awal">

    <input type="date"
        name="tanggal_akhir"
        id="tanggal-akhir"
        class="form-control filter-rentang"
        style="width: 155px;"
        value="{{ request('tanggal_akhir') }}"
        aria-label="Tanggal akhir">

    <button type="submit" class="btn btn-outline-primary">
        Filter
    </button>

    <a href="{{ route('transaksi.index') }}"
        class="btn btn-outline-secondary">
        Reset
    </a>

    <a href="{{ route('transaksi.export', request()->query()) }}"
        class="btn btn-outline-success">
        Export Laporan
    </a>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Kode Transaksi</th>
            <th>Tanggal</th>
            <th>Kasir</th>
            <th>Metode</th>
            <th>Total</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($transaksis as $t)
        <tr>
            <td>{{ $t->kode_transaksi }}</td>

            <td>
                {{ $t->created_at->format('d M Y, H:i') }}
            </td>

            <td>{{ $t->user->name ?? '-' }}</td>

            <td>
                <span class="badge bg-secondary text-uppercase">
                    {{ $t->metode_bayar }}
                </span>
            </td>

            <td>
                Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
            </td>

            <td>
                <a href="{{ route('transaksi.show', $t->id) }}"
                    class="btn btn-sm btn-outline-primary">
                    Detail
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">
                Belum ada transaksi.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $transaksis->links('pagination::bootstrap-5') }}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const periode = document.getElementById('periode');
        const tanggal = document.getElementById('tanggal');
        const tanggalAwal = document.getElementById('tanggal-awal');
        const tanggalAkhir = document.getElementById('tanggal-akhir');

        function aturFilter() {
            const rentang = periode.value === 'rentang';
            const semua = periode.value === 'semua';

            tanggal.classList.toggle(
                'd-none',
                semua || rentang
            );

            tanggalAwal.classList.toggle(
                'd-none',
                !rentang
            );

            tanggalAkhir.classList.toggle(
                'd-none',
                !rentang
            );

            tanggal.disabled = semua || rentang;
            tanggalAwal.disabled = !rentang;
            tanggalAkhir.disabled = !rentang;

            tanggalAwal.required = rentang;
            tanggalAkhir.required = rentang;
        }

        periode.addEventListener('change', aturFilter);

        aturFilter();
    });
</script>

@endsection 