@extends('layouts.produk')

@section('title', 'Tambah Kategori')

@section('content')

    <h2>Tambah Kategori Baru</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kategori.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama Kategori:</label>
            <input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Batal</a>
    </form>

@endsection