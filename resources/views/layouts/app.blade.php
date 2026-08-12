<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - Vault Penitipan Helm</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Tremor-style CSS -->
    <link href="{{ asset('css/tremor-vault.css') }}" rel="stylesheet">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="vault-sidebar">
        <div class="vault-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Vault Logo" class="vault-brand-logo">
            <div>
                <div>VAULT</div>
                <div style="font-size: 0.65rem; font-weight: 500; color: #94a3b8; letter-spacing: 0.05em;">PENITIPAN HELM</div>
            </div>
        </div>

        <ul class="vault-menu">
            <li class="vault-menu-section">Menu Utama</li>
            <li class="vault-menu-item">
                <a href="{{ route('dashboard') }}" class="vault-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="vault-menu-section">Operasional</li>
            <li class="vault-menu-item">
                <a href="{{ route('transaksi.create') }}" class="vault-menu-link {{ request()->routeIs('transaksi.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Titip Helm Baru</span>
                </a>
            </li>
            <li class="vault-menu-item">
                <a href="{{ route('transaksi.index') }}" class="vault-menu-link {{ request()->routeIs('transaksi.index', 'transaksi.show', 'transaksi.ambil') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span>Daftar Transaksi</span>
                </a>
            </li>
            <li class="vault-menu-item">
                <a href="{{ route('loker.index') }}" class="vault-menu-link {{ request()->routeIs('loker.index') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Status Loker</span>
                </a>
            </li>
            <li class="vault-menu-item">
                <a href="{{ route('pelanggan.index') }}" class="vault-menu-link {{ request()->routeIs('pelanggan.index') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Data Pelanggan</span>
                </a>
            </li>

            @if(auth()->user()->isAdmin())
                <li class="vault-menu-section">Administrasi</li>
                <li class="vault-menu-item">
                    <a href="{{ route('users.index') }}" class="vault-menu-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill"></i>
                        <span>Kelola Petugas</span>
                    </a>
                </li>
                <li class="vault-menu-item">
                    <a href="{{ route('tarif.index') }}" class="vault-menu-link {{ request()->routeIs('tarif.index') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin"></i>
                        <span>Kelola Tarif</span>
                    </a>
                </li>
                <li class="vault-menu-item">
                    <a href="{{ route('laporan.index') }}" class="vault-menu-link {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line-fill"></i>
                        <span>Laporan Laba/Rugi</span>
                    </a>
                </li>
            @endif
        </ul>
    </aside>

    <!-- Wrapper -->
    <div class="vault-wrapper">
        <!-- Top Navbar -->
        <header class="vault-navbar">
            <div class="d-flex align-items-center gap-3">
                <h5 class="m-0 fw-bold text-dark">{{ $title ?? 'Dashboard' }}</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-pill border py-1 px-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-weight: 700; font-size: 0.85rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="text-start d-none d-sm-block">
                            <div class="fw-semibold text-dark fs-7" style="font-size: 0.85rem; line-height: 1.2;">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.725rem;">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                        <i class="bi bi-chevron-down text-muted" style="font-size: 0.75rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <div class="fw-bold">{{ auth()->user()->name }}</div>
                                <div class="text-muted fs-7">{{ auth()->user()->email }}</div>
                            </div>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                    <i class="bi bi-box-arrow-right"></i> Keluar / Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="vault-content">
            <!-- Flash Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
