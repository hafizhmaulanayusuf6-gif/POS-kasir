@extends('layouts.main')

@section('title', 'Riwayat Transaksi')

@section('content')

    <h2>Riwayat Transaksi</h2>

    <form method="GET" class="mb-3 d-flex gap-2" style="max-width:300px;">
        <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
        <button type="submit" class="btn btn-outline-primary">Filter</button>
        @if (request('tanggal'))
            <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">Reset</a>
        @endif
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
                <td>{{ $t->created_at->format('d M Y, H:i') }}</td>
                <td>{{ $t->user->name ?? '-' }}</td>
                <td><span class="badge bg-secondary text-uppercase">{{ $t->metode_bayar }}</span></td>
                <td>Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('transaksi.show', $t->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $transaksis->links('pagination::bootstrap-5') }}
    </div>

@endsection