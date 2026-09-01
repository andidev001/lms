@php
    $sekolahData = \App\Models\Sekolah::first();
    $namaSekolah = ($sekolahData && $sekolahData->nama_sekolah) ? $sekolahData->nama_sekolah : config('app.name', 'LMS');
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $namaSekolah }} - Masuk ke Akun Anda</title>
    <link rel="icon" type="image/png" href="{{ ($sekolahData && $sekolahData->logo) ? asset('storage/' . $sekolahData->logo) : asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:300,400,500,600,700,800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --dark-navy: #1e293b;
            --slate-gray: #64748b;
            --border-color: #cbd5e1;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #dceafe;
            color: var(--dark-navy);
            /* Pastikan TIDAK ADA SCROLLBAR di desktop/monitor */
            overflow: hidden;
            position: relative;
        }

        /* Latar Belakang Gelombang & Lekungan (SVG Waves) */
        .bg-waves-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .bg-waves-svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Wrapper utama */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 3rem;
        }

        /* Responsive Grid Layout berukuran proporsional agar 100% pas tanpa scroll */
        .login-grid-container {
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            gap: 3.5rem;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 1140px;
            max-height: 94vh;
            margin: 0 auto;
        }

        /* Left Column Branding */
        .left-column {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            max-height: 100%;
        }

        .brand-logo-container {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .brand-logo-container:hover {
            opacity: 0.9;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .brand-icon-img {
            height: 52px;
            width: auto;
            max-width: 70px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .brand-text h1 {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--dark-navy);
            line-height: 1.15;
            letter-spacing: -0.4px;
            margin: 0;
            max-width: 420px;
            word-wrap: break-word;
        }

        .brand-text p {
            font-size: 0.86rem;
            font-weight: 500;
            color: var(--slate-gray);
            margin: 3px 0 0 0;
            letter-spacing: 0.2px;
        }

        .hero-title {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.25;
            color: var(--dark-navy);
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }

        .hero-title span {
            color: var(--primary-blue);
            display: block;
        }

        .hero-desc {
            font-size: 0.95rem;
            color: var(--slate-gray);
            line-height: 1.5;
            max-width: 90%;
            margin-bottom: 1.25rem;
            margin-top: 0;
        }

        .illustration-container {
            text-align: center;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .illustration-img {
            max-width: 100%;
            height: auto;
            max-height: 295px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 15px 25px rgba(30, 58, 138, 0.08));
            transition: transform 0.3s ease;
        }

        .illustration-img:hover {
            transform: translateY(-4px);
        }

        /* Right Column Form Card */
        .right-column {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .login-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 2.75rem 2.5rem;
            width: 100%;
            max-width: 430px;
            margin: 0 auto;
            box-shadow: 0 25px 50px -12px rgba(30, 58, 138, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.9);
            position: relative;
        }

        .card-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--dark-navy);
            margin: 0 0 0.25rem 0;
            letter-spacing: -0.5px;
        }

        .card-subtitle {
            color: var(--slate-gray);
            font-size: 0.88rem;
            margin: 0 0 1.85rem 0;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.86rem;
            color: var(--dark-navy);
            margin-bottom: 0.4rem;
            display: block;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.2s;
            display: flex;
            align-items: center;
        }

        .form-control-custom {
            width: 100%;
            height: 48px;
            padding: 0.65rem 1rem 0.65rem 44px;
            font-size: 0.92rem;
            color: var(--dark-navy);
            background-color: #ffffff;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            transition: all 0.2s ease-in-out;
            outline: none;
            font-family: inherit;
        }

        .form-control-custom:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.12);
        }

        .form-control-custom:focus ~ .input-icon {
            color: var(--primary-blue);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--slate-gray);
        }

        .btn-submit {
            width: 100%;
            height: 48px;
            background-color: var(--primary-blue);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            font-family: inherit;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .footer-text {
            text-align: center;
            margin-top: 2.25rem;
            font-size: 0.82rem;
            color: #94a3b8;
            letter-spacing: 0.2px;
        }

        .footer-text strong {
            color: var(--slate-gray);
            font-weight: 600;
        }

        .text-danger {
            color: #dc2626;
            font-size: 0.82rem;
            margin-top: 0.35rem;
        }

        /* Responsive Design untuk Tablet & Smartphone */
        @media (max-width: 991.98px) {
            html, body {
                overflow-y: auto;
                height: auto;
            }
            .login-wrapper {
                padding: 2rem 1.5rem;
                height: auto;
                min-height: 100vh;
            }
            .login-grid-container {
                grid-template-columns: 1fr;
                gap: 2rem;
                max-height: none;
            }
            .left-column {
                text-align: center;
            }
            .brand-logo-container {
                justify-content: center;
            }
            .brand-text h1 {
                margin: 0 auto;
            }
            .hero-title {
                font-size: 1.7rem;
                margin-top: 1rem;
                margin-bottom: 0.5rem;
            }
            .hero-desc {
                margin-left: auto;
                margin-right: auto;
                margin-bottom: 1rem;
            }
            .illustration-container {
                justify-content: center;
            }
            .illustration-img {
                max-height: 200px;
            }
            .login-card {
                padding: 2rem 1.5rem;
                max-width: 100%;
            }
        }

        /* Responsive Design Khusus untuk Smartphone / Mobile HP (< 768px) */
        @media (max-width: 767.98px) {
            .login-wrapper {
                padding: 1.25rem 1rem 2rem 1rem;
            }
            .login-grid-container {
                gap: 1.25rem;
            }
            .brand-logo-container {
                gap: 10px;
                margin-bottom: 0.25rem;
            }
            .brand-icon-img {
                height: 40px;
                max-width: 52px;
            }
            .brand-icon {
                width: 38px;
                height: 38px;
            }
            .brand-text h1 {
                font-size: 1.35rem;
                letter-spacing: -0.2px;
            }
            .brand-text p {
                font-size: 0.78rem;
                margin-top: 1px;
            }
            /* Sembunyikan judul & deskripsi promosi di layar HP agar form login langsung terlihat rapi & tidak memakan ruang scroll */
            .hero-title, .hero-desc {
                display: none;
            }
            .illustration-container {
                margin-top: 0.25rem;
                margin-bottom: 0.25rem;
            }
            /* Ukuran ilustrasi diperkemas agar berbanding lurus dan manis dengan layar HP */
            .illustration-img {
                max-height: 135px;
            }
            .login-card {
                padding: 1.75rem 1.35rem;
                border-radius: 20px;
                box-shadow: 0 15px 35px -5px rgba(30, 58, 138, 0.1), 0 0 0 1px rgba(226, 232, 240, 0.8);
            }
            .card-title {
                font-size: 1.4rem;
                margin-bottom: 0.2rem;
            }
            .card-subtitle {
                font-size: 0.84rem;
                margin-bottom: 1.35rem;
            }
            .form-control-custom {
                height: 46px;
                font-size: 0.88rem;
                padding-left: 40px;
            }
            .input-icon {
                left: 12px;
            }
            .btn-submit {
                height: 46px;
                font-size: 0.92rem;
            }
            .footer-text {
                margin-top: 1.5rem;
                font-size: 0.78rem;
            }
        }
    </style>
</head>
<body>
    <!-- Latar Belakang Lekungan & Gradasi Warna Modern -->
    <div class="bg-waves-container">
        <svg class="bg-waves-svg" viewBox="0 0 1440 900" preserveAspectRatio="none">
            <defs>
                <linearGradient id="waveGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#ffffff" stop-opacity="0.85"/>
                    <stop offset="100%" stop-color="#eff6ff" stop-opacity="0.95"/>
                </linearGradient>
                <linearGradient id="waveGrad2" x1="0%" y1="0%" x2="100%" y2="80%">
                    <stop offset="0%" stop-color="#cbe2ff" stop-opacity="0.75"/>
                    <stop offset="100%" stop-color="#b6d5ff" stop-opacity="0.85"/>
                </linearGradient>
            </defs>

            <!-- Latar belakang warna dasar -->
            <rect width="1440" height="900" fill="#ddecff" />
            
            <!-- Gelombang lapis pertama (Lekungan Biru Lembut) -->
            <path d="M 0 0 L 880 0 C 760 230 850 420 700 600 C 550 780 340 820 380 900 L 0 900 Z" fill="url(#waveGrad2)" />

            <!-- Gelombang lapis kedua (Lekungan Utama Putih/Biru Es tempat Teks & Ilustrasi berada) -->
            <path d="M 0 0 L 760 0 C 640 200 730 400 600 560 C 470 720 250 810 290 900 L 0 900 Z" fill="url(#waveGrad1)" />

            <!-- Gelombang aksen di pojok kiri atas -->
            <path d="M 0 0 L 580 0 C 480 160 530 300 420 440 C 310 580 90 640 0 720 Z" fill="#ffffff" opacity="0.65" />

            <!-- Pola titik-titik hiasan (Decorative Dots) -->
            <g fill="#ffffff" opacity="0.85" transform="translate(640, 310)">
                <circle cx="0" cy="0" r="3.5"/><circle cx="16" cy="0" r="3.5"/><circle cx="32" cy="0" r="3.5"/><circle cx="48" cy="0" r="3.5"/><circle cx="64" cy="0" r="3.5"/>
                <circle cx="0" cy="16" r="3.5"/><circle cx="16" cy="16" r="3.5"/><circle cx="32" cy="16" r="3.5"/><circle cx="48" cy="16" r="3.5"/><circle cx="64" cy="16" r="3.5"/>
                <circle cx="0" cy="32" r="3.5"/><circle cx="16" cy="32" r="3.5"/><circle cx="32" cy="32" r="3.5"/><circle cx="48" cy="32" r="3.5"/><circle cx="64" cy="32" r="3.5"/>
                <circle cx="0" cy="48" r="3.5"/><circle cx="16" cy="48" r="3.5"/><circle cx="32" cy="48" r="3.5"/><circle cx="48" cy="48" r="3.5"/><circle cx="64" cy="48" r="3.5"/>
            </g>
            <g fill="#93c5fd" opacity="0.45" transform="translate(648, 318)">
                <circle cx="0" cy="0" r="2.5"/><circle cx="16" cy="0" r="2.5"/><circle cx="32" cy="0" r="2.5"/><circle cx="48" cy="0" r="2.5"/><circle cx="64" cy="0" r="2.5"/>
            </g>

            <!-- Aksen gelembung lingkaran samar di latar -->
            <circle cx="820" cy="180" r="18" fill="#ffffff" opacity="0.4"/>
            <circle cx="580" cy="120" r="10" fill="#93c5fd" opacity="0.5"/>
            <circle cx="610" cy="740" r="24" fill="#ffffff" opacity="0.5"/>
            <circle cx="860" cy="760" r="14" fill="#a5ccff" opacity="0.6"/>
        </svg>
    </div>

    <!-- Kontainer Utama -->
    <div class="login-wrapper">
        <div class="login-grid-container">
            
            <!-- Left Column: Branding & Illustration -->
            <div class="left-column">
                <div>
                    <!-- Logo & Nama Sekolah -->
                    <a href="{{ url('/') }}" class="brand-logo-container">
                        @if($sekolahData && $sekolahData->logo)
                            <img src="{{ asset('storage/' . $sekolahData->logo) }}" alt="Logo {{ $namaSekolah }}" class="brand-icon-img">
                        @else
                            <!-- Default Graduation Cap SVG Icon jika belum ada logo di database -->
                            <svg class="brand-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M32 8L4 22L32 36L60 22L32 8Z" fill="#1e293b"/>
                                <path d="M14 31V45C14 45 20 52 32 52C44 52 50 45 50 45V31" stroke="#2563eb" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M56 26V48" stroke="#2563eb" stroke-width="4" stroke-linecap="round"/>
                                <circle cx="56" cy="50" r="3" fill="#2563eb"/>
                            </svg>
                        @endif
                        <div class="brand-text text-start">
                            <h1>{{ $namaSekolah }}</h1>
                            <p>Learning Management System</p>
                        </div>
                    </a>

                    <!-- Title & Description -->
                    <h2 class="hero-title">
                        Belajar Lebih Mudah,
                        <span>Kapan Saja, Di Mana Saja</span>
                    </h2>
                    <p class="hero-desc">
                        Akses materi pembelajaran, tugas, dan diskusi dalam satu platform terintegrasi.
                    </p>
                </div>

                <!-- Illustration -->
                <div class="illustration-container">
                    <img src="{{ asset('images/illustration.png') }}" 
                         alt="Ilustrasi Belajar" 
                         class="illustration-img"
                         onerror="this.onerror=null; this.src='{{ asset('images/illustration.svg') }}';">
                </div>
            </div>

            <!-- Right Column: Login Form Card -->
            <div class="right-column">
                <div class="login-card">
                    <div style="margin-bottom: 1.75rem;">
                        <h3 class="card-title">Selamat Datang!</h3>
                        <p class="card-subtitle">Masuk untuk melanjutkan ke akun Anda</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email / Username Field -->
                        <div style="margin-bottom: 1.35rem;">
                            <label for="email" class="form-label">Email atau Username</label>
                            <div class="input-group-custom">
                                <input id="email" type="text" 
                                       class="form-control-custom @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" 
                                       required autocomplete="username" autofocus 
                                       placeholder="Masukkan email atau username Anda">
                                <span class="input-icon">
                                    <!-- User SVG Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                            </div>
                            @error('email')
                                <div class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div style="margin-bottom: 2rem;">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <div class="input-group-custom">
                                <input id="password" type="password" 
                                       class="form-control-custom @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="current-password" 
                                       placeholder="Masukkan kata sandi Anda">
                                <span class="input-icon">
                                    <!-- Lock SVG Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <button type="button" class="toggle-password" id="togglePasswordBtn" aria-label="Toggle Password">
                                    <!-- Eye SVG Icon -->
                                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit">
                            Masuk
                        </button>

                        <!-- Copyright Footer -->
                        <div class="footer-text">
                            Copyright &copy; {{ date('Y') }} <strong>skolabs.web.id</strong> | Team Skolabs
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePasswordBtn = document.querySelector('#togglePasswordBtn');
            const passwordInput = document.querySelector('#password');
            const eyeIcon = document.querySelector('#eyeIcon');

            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    if (isPassword) {
                        // Eye off icon
                        eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                    } else {
                        // Eye icon
                        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                    }
                });
            }
        });
    </script>
</body>
</html>
