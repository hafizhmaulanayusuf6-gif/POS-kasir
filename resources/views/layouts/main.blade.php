<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'POS Kasir')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 230px;
            --sidebar-width-collapsed: 70px;
        }

        body {
            overflow-x: hidden;
        }

        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #1e2a38;
            transition: width 0.2s ease;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        #sidebar.collapsed {
            width: var(--sidebar-width-collapsed);
        }

        #sidebar .sidebar-brand {
            color: #fff;
            padding: 1rem;
            font-weight: bold;
            font-size: 1.1rem;
            white-space: nowrap;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            white-space: nowrap;
            overflow: hidden;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }

        #sidebar .nav-link i {
            font-size: 1.1rem;
            width: 24px;
            flex-shrink: 0;
        }

        #sidebar .nav-link .link-text {
            margin-left: 10px;
        }

        #sidebar.collapsed .link-text,
        #sidebar.collapsed .sidebar-brand-text {
            display: none;
        }

        #main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.2s ease;
            min-height: 100vh;
        }

        #main-content.collapsed {
            margin-left: var(--sidebar-width-collapsed);
        }

        #topbar {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.5rem;
        }
    </style>
</head>

<body>

    <div id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shop"></i>
            <span class="sidebar-brand-text">POS Kasir</span>
        </div>
        <ul class="nav flex-column">
            @if (Auth::user()->isAdmin())
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="bi bi-bar-chart"></i>
                    <span class="link-text">Dashboard Produk</span>
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard-kasir.index') }}">
                    <i class="bi bi-graph-up"></i>
                    <span class="link-text">Dashboard Penjualan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('kasir.index') }}">
                    <i class="bi bi-cart"></i>
                    <span class="link-text">Kasir</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('transaksi.index') }}">
                    <i class="bi bi-receipt"></i>
                    <span class="link-text">Riwayat Transaksi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('produk.index') }}">
                    <i class="bi bi-box-seam"></i>
                    <span class="link-text">Produk</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('kategori.index') }}">
                    <i class="bi bi-tags"></i>
                    <span class="link-text">Kategori</span>
                </a>
            </li>
        </ul>
    </div>

    <div id="main-content">
        <div id="topbar" class="d-flex justify-content-between align-items-center">
            <button id="sidebarToggle" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list"></i>
            </button>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    Logout ({{ Auth::user()->name }})
                </button>
            </form>
        </div>

        <div class="container-fluid p-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('main-content').classList.toggle('collapsed');
        });
    </script>
</body>

</html>