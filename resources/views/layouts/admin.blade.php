<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - MDT Al-Harus')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logo-title.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo-alharus.png') }}" alt="MDT Al-Harus" />
            <span>MDT Al-Harus</span>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kegiatan.index') }}" class="{{ request()->routeIs('admin.kegiatan.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i>
                        <span>Kegiatan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.guru.index') }}" class="{{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Guru</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.isma.index') }}" class="{{ request()->routeIs('admin.isma.*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i>
                        <span>Isma</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.program.index') }}" class="{{ request()->routeIs('admin.program.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard"></i>
                        <span>Program Unggulan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.fasilitas.index') }}" class="{{ request()->routeIs('admin.fasilitas.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Fasilitas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.infak.index') }}" class="{{ request()->routeIs('admin.infak.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Infak</span>
            </a>
                </li>
                <li>
            <a href="{{ route('admin.santri.index') }}" class="{{ request()->routeIs('admin.santri.*') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i>
                <span>Santri</span>
            </a>
        </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <!-- Info User -->
            <div class="sidebar-user">
                <img src="{{ asset('images/logo-title.ico') }}" alt="Avatar" class="sidebar-avatar">
                <div class="sidebar-user-info">
                    <span class="sidebar-username">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <span class="sidebar-role">{{ Auth::user()->role ?? 'admin' }}</span>
                </div>
            </div>
            
            <a href="{{ route('home') }}" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Lihat Website</span>
            </a>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="admin-main" id="adminMain">
        <!-- Top Navbar -->
        <header class="admin-header">
            <div class="header-left">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="header-right">
                <div class="admin-user">
                    <span class="user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <img src="{{ asset('images/logo-title.ico') }}" alt="Avatar" class="user-avatar">
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; cursor: pointer; color: #dc3545; font-size: 1.2rem;" title="Logout">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="admin-content">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>