<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'LMS SKOLABS') }} - @yield('title', 'Admin Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            background: #ffffff; /* White background */
            color: #334155;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            overflow-y: auto;
            transition: transform 0.3s ease;
        }
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e293b;
            text-decoration: none;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .sidebar-brand span {
            color: #667eea;
        }
        .nav-link {
            color: #64748b;
            padding: 0.8rem 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }
        .nav-link:hover, .nav-link.active {
            color: #667eea;
            background: #f1f5f9;
            border-left: 4px solid #667eea;
        }
        .nav-link[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
        .nav-link i {
            font-size: 1.2rem;
            width: 25px;
            text-align: center;
        }
        .content-header {
            padding: 1.5rem 2rem;
            background-color: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .content-header {
                padding: 1rem;
            }
        }
        .content-body {
            padding: 2rem;
        }
        .user-dropdown {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1); 
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2); 
            border-radius: 10px;
        }
    </style>
</head>
<body>
    @php
        $sekolahData = \App\Models\Sekolah::first();
        $appName = $sekolahData ? $sekolahData->nama_sekolah : config('app.name', 'LMS');
    @endphp
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar d-flex flex-column">
            <a href="{{ route('admin.dashboard') ?? '#' }}" class="sidebar-brand d-flex align-items-center" style="gap: 10px; font-size: 1.25rem;">
                @if($sekolahData && $sekolahData->logo)
                    <img src="{{ asset('storage/' . $sekolahData->logo) }}" alt="Logo" style="height: 32px; width: auto; object-fit: contain;">
                @else
                    <i class="bi bi-mortarboard-fill" style="color: #667eea; font-size: 1.5rem;"></i> 
                @endif
                <span class="text-truncate" style="max-width: 150px;">{{ $appName }}</span>
            </a>
            
            <div class="py-3 px-4 mt-2 text-uppercase small fw-bold" style="color: #64748b; letter-spacing: 1px; font-size: 0.75rem;">
                Menu Admin
            </div>
            
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') ?? '#' }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*', 'gurus.*', 'siswas.*', 'kelas.*', 'mapels.*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#collapseDataMaster" role="button" aria-expanded="{{ request()->routeIs('users.*', 'gurus.*', 'siswas.*', 'kelas.*', 'mapels.*') ? 'true' : 'false' }}" aria-controls="collapseDataMaster">
                        <i class="bi bi-database"></i> Data Master 
                        <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; transition: transform 0.3s;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('users.*', 'gurus.*', 'siswas.*', 'kelas.*', 'mapels.*') ? 'show' : '' }}" id="collapseDataMaster">
                        <ul class="nav flex-column ms-3 mt-1" style="border-left: 1px solid #e2e8f0;">
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'text-primary fw-bold' : '' }}" style="padding: 0.5rem 1.5rem; font-size: 0.9rem; border-left: none;">
                                    <i class="bi bi-people" style="font-size: 1rem;"></i> Kelola User
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('gurus.index') }}" class="nav-link {{ request()->routeIs('gurus.*') ? 'text-primary fw-bold' : '' }}" style="padding: 0.5rem 1.5rem; font-size: 0.9rem; border-left: none;">
                                    <i class="bi bi-person-workspace" style="font-size: 1rem;"></i> Data Guru
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('siswas.index') }}" class="nav-link {{ request()->routeIs('siswas.*') ? 'text-primary fw-bold' : '' }}" style="padding: 0.5rem 1.5rem; font-size: 0.9rem; border-left: none;">
                                    <i class="bi bi-person-badge" style="font-size: 1rem;"></i> Data Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('kelas.index') }}" class="nav-link {{ request()->routeIs('kelas.*') ? 'text-primary fw-bold' : '' }}" style="padding: 0.5rem 1.5rem; font-size: 0.9rem; border-left: none;">
                                    <i class="bi bi-buildings" style="font-size: 1rem;"></i> Kelola Kelas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mapels.index') }}" class="nav-link {{ request()->routeIs('mapels.*') ? 'text-primary fw-bold' : '' }}" style="padding: 0.5rem 1.5rem; font-size: 0.9rem; border-left: none;">
                                    <i class="bi bi-book" style="font-size: 1rem;"></i> Mata Pelajaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('tahun-ajarans.index') }}" class="nav-link {{ request()->routeIs('tahun-ajarans.*') ? 'text-primary fw-bold' : '' }}" style="padding: 0.5rem 1.5rem; font-size: 0.9rem; border-left: none;">
                                    <i class="bi bi-calendar3" style="font-size: 1rem;"></i> Tahun Ajaran
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pengumumans.index') }}" class="nav-link {{ request()->routeIs('pengumumans.*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone"></i> Kelola Pengumuman
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporans.akademik') }}" class="nav-link {{ request()->routeIs('laporans.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Kelola Laporan
                    </a>
                </li>
                
                <li class="nav-item mt-3">
                    <a class="nav-link {{ request()->routeIs('sekolah.*') || request()->routeIs('backup.*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#collapsePengaturan" role="button" aria-expanded="{{ request()->routeIs('sekolah.*') || request()->routeIs('backup.*') ? 'true' : 'false' }}" aria-controls="collapsePengaturan">
                        <i class="bi bi-gear"></i> Pengaturan
                        <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; transition: transform 0.3s;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('sekolah.*') || request()->routeIs('backup.*') ? 'show' : '' }}" id="collapsePengaturan">
                        <ul class="nav flex-column ms-3 mt-1" style="border-left: 1px solid #e2e8f0;">
                            <li class="nav-item">
                                <a href="{{ route('sekolah.index') }}" class="nav-link {{ request()->routeIs('sekolah.*') ? 'text-primary fw-bold' : '' }}" style="padding: 0.5rem 1.5rem; font-size: 0.9rem; border-left: none;">
                                    <i class="bi bi-building" style="font-size: 1rem;"></i> Profil Sekolah
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('backup.index') }}" class="nav-link {{ request()->routeIs('backup.*') ? 'text-primary fw-bold' : '' }}" style="padding: 0.5rem 1.5rem; font-size: 0.9rem; border-left: none;">
                                    <i class="bi bi-database-gear" style="font-size: 1rem;"></i> Backup & Restore
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            
            <div class="mt-auto p-3">
                <a class="nav-link text-danger p-2 d-flex align-items-center gap-2 rounded" style="border-left: none; background: rgba(239, 68, 68, 0.1);" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right text-danger"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content flex-grow-1">
            <header class="content-header shadow-sm">
                <div class="d-flex align-items-center" style="min-width: 0;">
                    <button class="btn btn-light d-md-none me-2 flex-shrink-0" id="sidebarToggle">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div style="min-width: 0;">
                        <h4 class="mb-0 fw-bold text-dark d-flex flex-wrap align-items-center gap-2">
                            @yield('title', 'Dashboard')
                            @if(isset($activeTahunAjaran) && $activeTahunAjaran)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill fw-normal" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-calendar-check me-1"></i> <span class="d-none d-sm-inline">Tahun Ajaran Aktif: </span>{{ $activeTahunAjaran->nama_tahun }} - {{ $activeTahunAjaran->semester }}
                                </span>
                            @endif
                        </h4>
                        <span class="text-muted small">@yield('subtitle', 'Selamat datang di panel admin')</span>
                    </div>
                </div>
                
                <div class="dropdown">
                    <div class="user-dropdown text-dark dropdown-toggle fw-semibold" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover border border-secondary border-opacity-25" style="width: 38px; height: 38px;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 1.2rem;">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                        @endif
                        <div class="d-none d-md-block text-start">
                            <div style="line-height: 1;">{{ Auth::user()->name ?? 'Administrator' }}</div>
                            <small class="text-muted fw-normal" style="font-size: 0.75rem;">Admin</small>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-lg-end shadow border-0 mt-2" aria-labelledby="userMenu">
                        <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form-dropdown').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                            <form id="logout-form-dropdown" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </header>
            
            <div class="content-body">
                @yield('content')
            </div>
            
            <footer class="footer mt-auto py-3 text-center" style="border-top: 1px solid #e2e8f0; background-color: transparent;">
                <div class="container-fluid">
                    <span class="text-muted small" style="font-size: 0.8rem;">Copyright &copy; {{ date('Y') }} skolabs.web.id | Team Skolabs</span>
                </div>
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            if(toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });
            }
            if(overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    @stack('scripts')
    @include('partials.sweetalert')
</body>
</html>
