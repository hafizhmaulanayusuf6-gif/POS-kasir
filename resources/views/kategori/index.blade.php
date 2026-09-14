@extends('layouts.produk')

@section('title', 'Data Kategori')

@section('content')

    <h2>Daftar Kategori</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('produk.index') }}" class="btn btn-secondary">← Kembali ke Produk</a>
            @if (Auth::user()->isAdmin())
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                    + Tambah Kategori
                </button>
            @endif
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kategoris as $index => $k)
            <tr>
                <td>{{ $kategoris->firstItem() + $index }}</td>
                <td>{{ $k->nama_kategori }}</td>
                <td>
                    @if (Auth::user()->isAdmin())
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditKategori{{ $k->id }}">
                            Edit
                        </button>

                        <form action="{{ route('kategori.destroy', $k->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin ingin menghapus kategori ini? Produk yang memakainya akan kehilangan kategori.')">
                                Hapus
                            </button>
                        </form>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Belum ada kategori.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $kategoris->links('pagination::bootstrap-5') }}
    </div>

    {{-- ============ MODAL TAMBAH KATEGORI ============ --}}
    @if (Auth::user()->isAdmin())
    <div class="modal fade" id="modalTambahKategori" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kategori.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kategori Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && !old('kategori_id'))
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label>Nama Kategori:</label>
                            <input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ MODAL EDIT KATEGORI (1 per baris) ============ --}}
    @foreach ($kategoris as $k)
    <div class="modal fade" id="modalEditKategori{{ $k->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kategori.update', $k->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="kategori_id" value="{{ $k->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kategori: {{ $k->nama_kategori }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('kategori_id') == $k->id)
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label>Nama Kategori:</label>
                            <input type="text" name="nama_kategori" class="form-control"
                                value="{{ old('kategori_id') == $k->id ? old('nama_kategori') : $k->nama_kategori }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    @endif

    {{-- Script: otomatis buka modal lagi kalau validasi gagal --}}
    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (old('kategori_id'))
                var modal = new bootstrap.Modal(document.getElementById('modalEditKategori{{ old("kategori_id") }}'));
            @else
                var modal = new bootstrap.Modal(document.getElementById('modalTambahKategori'));
            @endif
            modal.show();
        });
    </script>
    @endif

@endsection