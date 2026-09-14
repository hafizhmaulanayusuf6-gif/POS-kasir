@extends('layouts.produk')

@section('title', 'Data Produk')

@section('content')

    <h2>Daftar Produk</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            @if (Auth::user()->isAdmin())
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    + Tambah Produk
                </button>
                <a href="{{ route('kategori.index') }}" class="btn btn-outline-dark">Kelola Kategori</a>
            @endif
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger">Logout ({{ Auth::user()->name }})</button>
        </form>
    </div>

    <form action="{{ route('produk.index') }}" method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Cari nama produk..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-primary">Cari</button>
        @if (request('search'))
        <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary">Reset</a>
        @endif
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Nama Produk</th>
                <th>
                    <a href="{{ route('produk.index', ['search' => request('search'), 'sort' => 'harga', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">
                        Harga
                        @if(request('sort') == 'harga')
                        {{ request('direction') == 'asc' ? '▲' : '▼' }}
                        @endif
                    </a>
                </th>
                <th>Stok</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produks as $index => $p)
            <tr>
                <td>{{ $produks->firstItem() + $index }}</td>
                <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ $p->nama_produk }}</td>
                <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                <td>{{ $p->stok }}</td>
                <td>
                    @if ($p->gambar)
                        <img src="{{ asset('storage/' . $p->gambar) }}" width="60" class="rounded">
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if (Auth::user()->isAdmin())
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id }}">
                            Edit
                        </button>

                        <form action="{{ route('produk.destroy', $p->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin ingin menghapus produk ini?')">
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
                <td colspan="7" class="text-center">Belum ada data produk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $produks->links('pagination::bootstrap-5') }}
    </div>

    {{-- ============ MODAL TAMBAH PRODUK ============ --}}
    @if (Auth::user()->isAdmin())
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Produk Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && !old('produk_id'))
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label>Nama Produk:</label>
                            <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk') }}">
                        </div>

                        <div class="mb-3">
                            <label>Harga:</label>
                            <input type="number" name="harga" class="form-control" value="{{ old('harga') }}">
                        </div>

                        <div class="mb-3">
                            <label>Stok:</label>
                            <input type="number" name="stok" class="form-control" value="{{ old('stok') }}">
                        </div>

                        <div class="mb-3">
                            <label>Kategori:</label>
                            <select name="kategori_id" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Gambar Produk:</label>
                            <input type="file" name="gambar" class="form-control">
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

    {{-- ============ MODAL EDIT PRODUK (1 per baris) ============ --}}
    @foreach ($produks as $p)
    <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('produk.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="produk_id" value="{{ $p->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Produk: {{ $p->nama_produk }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('produk_id') == $p->id)
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label>Nama Produk:</label>
                            <input type="text" name="nama_produk" class="form-control"
                                value="{{ old('produk_id') == $p->id ? old('nama_produk') : $p->nama_produk }}">
                        </div>

                        <div class="mb-3">
                            <label>Harga:</label>
                            <input type="number" name="harga" class="form-control"
                                value="{{ old('produk_id') == $p->id ? old('harga') : $p->harga }}">
                        </div>

                        <div class="mb-3">
                            <label>Stok:</label>
                            <input type="number" name="stok" class="form-control"
                                value="{{ old('produk_id') == $p->id ? old('stok') : $p->stok }}">
                        </div>

                        <div class="mb-3">
                            <label>Kategori:</label>
                            <select name="kategori_id" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ (old('produk_id') == $p->id ? old('kategori_id') : $p->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Gambar Produk:</label>
                            @if ($p->gambar)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $p->gambar) }}" width="80" class="rounded">
                                </div>
                            @endif
                            <input type="file" name="gambar" class="form-control">
                            <small class="text-muted">Kosongkan kalau tidak ingin ganti gambar.</small>
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
            @if (old('produk_id'))
                var modal = new bootstrap.Modal(document.getElementById('modalEdit{{ old("produk_id") }}'));
            @else
                var modal = new bootstrap.Modal(document.getElementById('modalTambah'));
            @endif
            modal.show();
        });
    </script>
    @endif

@endsection