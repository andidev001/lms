<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'LMS SKOLABS') }} - @yield('title', 'Siswa Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        /* Navbar Styling */
        .top-navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.8rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e293b;
        }
        .navbar-brand span {
            color: #38bdf8;
        }
        
        .nav-link {
            color: #64748b;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #0284c7;
            background-color: #f0f9ff;
        }

        .main-content {
            padding-top: 2rem;
            padding-bottom: 2rem;
            min-height: calc(100vh - 140px); /* Adjust based on navbar/footer height */
        }
        
        .content-header {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    
    @php
        $sekolahData = \App\Models\Sekolah::first();
        $appName = $sekolahData ? $sekolahData->nama_sekolah : config('app.name', 'LMS');
    @endphp
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid px-4 px-md-5">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('siswa.dashboard') ?? '#' }}" style="gap: 10px;">
                @if($sekolahData && $sekolahData->logo)
                    <img src="{{ asset('storage/' . $sekolahData->logo) }}" alt="Logo" style="height: 32px; width: auto; object-fit: contain;">
                @else
                    <i class="bi bi-backpack" style="color: #38bdf8; font-size: 1.5rem;"></i> 
                @endif
                <span class="text-truncate text-dark" style="max-width: 150px; font-size: 1.25rem;">{{ $appName }}</span>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}" href="{{ route('siswa.dashboard') ?? '#' }}">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.belajar*') || request()->routeIs('siswa.mapels.*') ? 'active' : '' }}" href="{{ route('siswa.belajar') }}">
                            <i class="bi bi-book"></i> Ruang Belajar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.tugas.*') ? 'active' : '' }}" href="{{ route('siswa.tugas.index') }}">
                            <i class="bi bi-journal-check"></i> Tugas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.nilai.*') ? 'active' : '' }}" href="{{ route('siswa.nilai.index') }}">
                            <i class="bi bi-award"></i> Nilai
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center mt-3 mt-lg-0">
                    <div class="dropdown">
                        <div class="d-flex align-items-center gap-2 cursor-pointer dropdown-toggle" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover shadow-sm border border-secondary border-opacity-25" style="width: 40px; height: 40px;">
                            @else
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                    {{ substr(Auth::user()->name ?? 'S', 0, 1) }}
                                </div>
                            @endif
                            <div class="d-none d-sm-block text-start me-2">
                                <div class="fw-bold text-dark" style="line-height: 1;">{{ Auth::user()->name ?? 'Siswa' }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">Siswa</small>
                            </div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-lg-end shadow border-0 mt-3 rounded-4" aria-labelledby="userMenu">
                            <li><a class="dropdown-item py-2 px-4" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 px-4 text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content container-fluid px-4 px-md-5">
        
        <!-- Header Content -->
        <header class="content-header">
            <div>
                <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-3">
                    @yield('title', 'Dashboard Siswa')
                    @if(isset($activeTahunAjaran) && $activeTahunAjaran)
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill fw-normal" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <i class="bi bi-calendar-check me-1"></i> {{ $activeTahunAjaran->nama_tahun }} - {{ $activeTahunAjaran->semester }}
                        </span>
                    @endif
                </h4>
                <span class="text-muted small">@yield('subtitle', 'Selamat datang di area belajar')</span>
            </div>
            
            <div class="d-none d-md-block">
                <!-- Ruang untuk tombol tambahan atau breadcrumb jika ada -->
            </div>
        </header>
        
        <!-- View Content -->
        <div>
            @yield('content')
        </div>
        
        <footer class="footer mt-auto py-3 text-center" style="border-top: 1px solid #e2e8f0; background-color: transparent;">
            <div class="container-fluid">
                <span class="text-muted small" style="font-size: 0.8rem;">Copyright &copy; {{ date('Y') }} skolabs.web.id | Team Skolabs</span>
            </div>
        </footer>
        
    </main>
    
    @include('partials.sweetalert')
</body>
</html>
