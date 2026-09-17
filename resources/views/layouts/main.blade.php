<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'POS Kasir')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('kasir.index') }}">POS Kasir</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    @if (Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard Produk</a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard-kasir.index') }}">Dashboard-Kasir</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('kasir.index') }}">Kasir</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('transaksi.index') }}">Riwayat Transaksi</a>
                    </li>
                    @if (Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('produk.index') }}">Produk</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('kategori.index') }}">Kategori</a>
                    </li>
                    @endif
                </ul>
                <form action="{{ route('logout') }}" method="POST" class="d-flex">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout ({{ Auth::user()->name }})</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>