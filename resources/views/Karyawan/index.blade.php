@extends('layouts.main')

@section('title', 'Kelola Karyawan')

@section('content')

<h2>Kelola Karyawan</h2>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    {{ $message }}
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger">
    {{ $message }}
</div>
@endif

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($karyawans as $index => $k)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $k->name }}</td>
            <td>{{ $k->email }}</td>
            <td>{{ $k->isAdmin() ? 'Admin' : 'Kasir' }}</td>
            <td>
                @if ($k->is_active)
                <span class="badge text-bg-success">Aktif</span>
                @else
                <span class="badge text-bg-secondary">Nonaktif</span>
                @endif
            </td>
            <td>
                @if ($k->id === Auth::id())
                <span class="text-muted">Akun Anda</span>
                @else
                <form action="{{ route('karyawan.status', $k->id) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('PATCH')
                    @if ($k->is_active)
                    <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Nonaktifkan akun ini? Karyawan tidak akan bisa login. Riwayat transaksinya tetap tersimpan.')">
                        Nonaktifkan
                    </button>
                    @else
                    <button type="submit" class="btn btn-sm btn-success">
                        Aktifkan
                    </button>
                    @endif
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">Belum ada karyawan.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection