@extends('layouts.produk')

@section('title', 'Tambah Produk')

@section('content')

<h2>Tambah Produk Baru</h2>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Nama Produk:</label>
        <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk') }}" required>
    </div>

    <div class="mb-3">
        <label>Harga:</label>
        <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" required>
    </div>

    <div class="mb-3">
        <label>Stok:</label>
        <input type="number" name="stok" class="form-control" value="{{ old('stok') }}" required>
    </div>

    <div class="mb-3">
        <label>Gambar Produk:</label>
        <input type="file" name="gambar" class="form-control">
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

    <button type="submit" class="btn btn-success">Simpan Data</button>
    <a href="{{ route('produk.index') }}" class="btn btn-secondary">Batal</a>
</form>

@endsection