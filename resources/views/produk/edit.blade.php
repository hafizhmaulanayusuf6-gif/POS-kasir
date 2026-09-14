@extends('layouts.produk')

@section('title', 'Edit Produk')

@section('content')

<h2>Edit Produk</h2>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Nama Produk:</label>
        <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
    </div>

    <div class="mb-3">
        <label>Harga:</label>
        <input type="number" name="harga" class="form-control" value="{{ old('harga', $produk->harga) }}" required>
    </div>

    <div class="mb-3">
        <label>Stok:</label>
        <input type="number" name="stok" class="form-control" value="{{ old('stok', $produk->stok) }}" required>
    </div>

    <div class="mb-3">
        <label>Gambar Produk:</label>
        @if ($produk->gambar)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $produk->gambar) }}" width="100" class="rounded">
        </div>
        @endif
        <input type="file" name="gambar" class="form-control">
        <small class="text-muted">Kosongkan kalau tidak ingin ganti gambar.</small>
    </div>

    <div class="mb-3">
        <label>Kategori:</label>
        <select name="kategori_id" class="form-control">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($kategoris as $kategori)
            <option value="{{ $kategori->id }}" {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                {{ $kategori->nama_kategori }}
            </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Update Data</button>
    <a href="{{ route('produk.index') }}" class="btn btn-secondary">Batal</a>
</form>

@endsection