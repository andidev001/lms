@php
    $sekolahData = \App\Models\Sekolah::first();
    $appName = ($sekolahData && $sekolahData->nama_sekolah) ? $sekolahData->nama_sekolah : config('app.name', 'LMS');
    $totalQuestions = count($materi->pretest_questions);
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian Post-Test: {{ $materi->judul }} - {{ $appName }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700,800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        /* 100vh & 100dvh untuk menjamin pas 1 layar di PC maupun HP (tanpa scrollbar luar) */
        body, html {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            height: 100vh;
            height: 100dvh;
            width: 100vw;
            overflow: hidden !important;
        }
        
        .cbt-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            height: 100dvh;
            width: 100vw;
            overflow: hidden;
        }

        /* Top Bar / Navbar */
        .cbt-navbar {
            height: 64px;
            background-color: #1e293b;
            border-bottom: 1px solid #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 1000;
            flex-shrink: 0;
        }

        /* Workspace Utama */
        .cbt-workspace {
            flex-grow: 1;
            height: calc(100dvh - 64px);
            display: flex;
            padding: 1.25rem 1.5rem;
            gap: 1.5rem;
            overflow: hidden;
        }

        /* Panel Kiri: Kotak Worksheet Soal */
        .worksheet-panel {
            flex: 1;
            background-color: #ffffff;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        }

        /* Custom Slide untuk Mencegah Bentrok dengan class d-flex Bootstrap (!important) */
        .cbt-slide {
            display: flex;
            flex-direction: column;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }
        .cbt-slide.d-none-custom {
            display: none !important;
        }

        .worksheet-header {
            padding: 1rem 1.75rem;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .worksheet-body {
            flex-grow: 1;
            padding: 1.75rem 2.25rem;
            overflow-y: auto;
        }

        .worksheet-footer {
            padding: 1rem 1.75rem;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        /* Panel Kanan: Sidebar Navigasi (PC / Laptop) */
        .nav-panel {
            width: 310px;
            background-color: #ffffff;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            flex-shrink: 0;
        }

        .nav-header {
            padding: 1.25rem 1.5rem;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .nav-body {
            flex-grow: 1;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .nav-footer {
            padding: 1.25rem 1.5rem;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        /* Grid Nomor Soal */
        .question-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .nav-pill {
            height: 44px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            background-color: #ffffff;
            color: #64748b;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }

        .nav-pill:hover {
            border-color: #94a3b8;
            background-color: #f8fafc;
        }

        .nav-pill.active {
            border-color: #2563eb !important;
            color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            font-weight: 800;
        }

        .nav-pill.answered {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff !important;
        }

        .nav-pill.answered.active {
            background-color: #1d4ed8;
            border-color: #0f172a !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.4);
        }

        /* Opsi Pilihan Ganda */
        .option-item-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.85rem 1.15rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.15s;
            background-color: #ffffff;
            margin-bottom: 0.75rem;
            user-select: none;
        }

        .option-item-card:hover {
            border-color: #cbd5e1;
            background-color: #f8fafc;
        }

        .option-item-card.selected {
            border-color: #2563eb !important;
            background-color: rgba(37, 99, 235, 0.06) !important;
        }

        .option-item-card.selected .option-letter-badge {
            background-color: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
        }

        .option-letter-badge {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.85rem;
            flex-shrink: 0;
            transition: all 0.15s;
            font-size: 0.9rem;
        }

        /* Perbaikan render HTML paragraf */
        .question-text p:last-child {
            margin-bottom: 0;
        }
        .option-text p {
            display: inline;
            margin-bottom: 0;
        }

        /* DESAIN KHUSUS HP & MOBILE (Max Width < 992px) */
        @media (max-width: 991.98px) {
            .cbt-navbar {
                height: 56px;
                padding: 0 0.85rem;
            }
            .cbt-workspace {
                height: calc(100dvh - 56px);
                padding: 0.4rem;
                gap: 0;
            }
            .nav-panel {
                display: none !important;
            }
            .worksheet-panel {
                border-radius: 12px;
            }
            .worksheet-header {
                padding: 0.65rem 0.85rem;
            }
            .worksheet-body {
                padding: 1rem 0.85rem;
            }
            .worksheet-footer {
                padding: 0.65rem 0.85rem;
            }
            .option-item-card {
                padding: 0.75rem 0.85rem;
            }
            .question-text {
                font-size: 1.05rem !important;
                margin-bottom: 1rem !important;
                padding-bottom: 0.75rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="cbt-container">
        <!-- Top Navigation Bar -->
        <header class="cbt-navbar">
            <div class="d-flex align-items-center gap-2 text-truncate">
                @if($sekolahData && $sekolahData->logo)
                    <img src="{{ asset('storage/' . $sekolahData->logo) }}" alt="Logo" style="height: 30px; width: auto; object-fit: contain;">
                @else
                    <div class="bg-primary text-white rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="bi bi-mortarboard-fill fs-6"></i>
                    </div>
                @endif
                <span class="fw-bold text-white fs-6 text-truncate" style="max-width: 210px;">Post-Test: {{ $materi->judul }}</span>
                
                <!-- Badge Indikator Auto-Save -->
                <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-50 px-3 py-1.5 rounded-pill text-nowrap ms-2 d-none d-md-inline-block" id="autoSaveStatus">
                    <i class="bi bi-shield-check text-success me-1"></i> Auto-Save Aktif
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <a href="{{ route('siswa.belajar') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5 fw-semibold text-nowrap small" onclick="confirmExitUjian(event, this.href);">
                    <i class="bi bi-box-arrow-left me-1"></i> Keluar
                </a>
            </div>
        </header>

        <!-- SPA Form & Workspace -->
        <form action="{{ route('siswa.mapels.materis.pretests.submit', [$mapel->id, $materi->id]) }}" method="POST" id="spaPostTestForm" class="cbt-workspace">
            @csrf
            
            <!-- Worksheet Panel (1-Page Full Viewport) -->
            <div class="worksheet-panel">
                @foreach($materi->pretest_questions as $index => $question)
                    <div class="cbt-slide {{ $index === 0 ? '' : 'd-none-custom' }}" id="slide-{{ $index }}">
                        <!-- Header Soal -->
                        <div class="worksheet-header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">
                                    No. {{ $index + 1 }}
                                </span>
                                <span class="text-muted small fw-medium d-none d-sm-inline">dari {{ $totalQuestions }} soal</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <!-- Tombol Navigasi Khusus HP -->
                                <button type="button" class="btn btn-sm btn-outline-primary d-lg-none fw-bold px-3 py-1 rounded-pill text-nowrap shadow-sm" data-bs-toggle="modal" data-bs-target="#mobileNavModal">
                                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> Daftar Soal (<span class="mobile-prog-text">0/{{ $totalQuestions }}</span>)
                                </button>

                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill status-badge-{{ $question->id }}">
                                    <i class="bi bi-circle me-1 text-muted"></i> Belum Dijawab
                                </span>
                            </div>
                        </div>

                        <!-- Body Soal -->
                        <div class="worksheet-body">
                            <!-- Pertanyaan -->
                            <div class="fs-5 fw-bold text-dark mb-4 pb-3 border-bottom question-text" style="line-height: 1.6;">
                                {!! $question->pertanyaan !!}
                            </div>

                            <!-- Pilihan Jawaban A - E -->
                            <div class="options-container mt-2">
                                @foreach(['A' => $question->opsi_a, 'B' => $question->opsi_b, 'C' => $question->opsi_c, 'D' => $question->opsi_d, 'E' => $question->opsi_e] as $letter => $opsi)
                                    @if($opsi)
                                        <label class="option-item-card" id="card_{{ $question->id }}_{{ $letter }}">
                                            <input type="radio" name="jawaban_{{ $question->id }}" value="{{ $letter }}" class="d-none option-radio" data-index="{{ $index }}" data-question-id="{{ $question->id }}" data-letter="{{ $letter }}">
                                            <span class="option-letter-badge">{{ $letter }}</span>
                                            <div class="option-text flex-grow-1 text-dark">
                                                {!! $opsi !!}
                                            </div>
                                            <i class="bi bi-check-circle-fill text-primary fs-5 ms-2 opacity-0 check-indicator"></i>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Footer Navigasi Pindah Soal -->
                        <div class="worksheet-footer">
                            @if($index > 0)
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 px-sm-4 py-2 fw-semibold small" onclick="navToQuestion({{ $index - 1 }})">
                                    <i class="bi bi-arrow-left me-1 me-sm-2"></i> Sebelumnya
                                </button>
                            @else
                                <div></div>
                            @endif

                            @if($index < $totalQuestions - 1)
                                <button type="button" class="btn btn-primary rounded-pill px-4 px-sm-5 py-2 fw-bold shadow-sm small" onclick="navToQuestion({{ $index + 1 }})">
                                    Selanjutnya <i class="bi bi-arrow-right ms-1 ms-sm-2"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-success rounded-pill px-4 px-sm-5 py-2 fw-bold shadow-sm small text-nowrap" onclick="confirmSubmitUjian(event);">
                                    <i class="bi bi-send-check me-1 me-sm-2"></i> Kirim Ujian
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Right Pane: Sidebar Navigasi (PC / Laptop) -->
            <div class="nav-panel d-none d-lg-flex">
                <div class="nav-header">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Navigasi Soal</h6>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill small"><i class="bi bi-shield-check me-1"></i>Auto-Save</span>
                    </div>
                    <p class="text-muted small mb-0">Klik pada nomor untuk melompat instan.</p>
                </div>
                
                <div class="nav-body">
                    <div class="question-grid">
                        @foreach($materi->pretest_questions as $index => $question)
                            <div class="nav-pill pill-{{ $index }} {{ $index === 0 ? 'active' : '' }}" onclick="navToQuestion({{ $index }})" title="Soal Nomor {{ $index + 1 }}">
                                {{ $index + 1 }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Keterangan Warna -->
                    <div class="mt-4 pt-3 border-top d-flex flex-column gap-2 small">
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <span class="badge bg-primary p-2 rounded-2" style="width: 16px; height: 16px; display: inline-block;"></span>
                            <span>Sudah Terjawab</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <span class="badge bg-white border border-secondary border-2 p-2 rounded-2" style="width: 16px; height: 16px; display: inline-block;"></span>
                            <span>Belum Terjawab</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <span class="badge bg-light border border-primary border-2 p-2 rounded-2" style="width: 16px; height: 16px; display: inline-block; box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);"></span>
                            <span>Posisi Soal Saat Ini</span>
                        </div>
                    </div>
                </div>

                <div class="nav-footer">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-muted">Progress Terjawab:</span>
                        <span class="badge bg-primary rounded-pill px-3" id="progressText">0 / {{ $totalQuestions }}</span>
                    </div>
                    <div class="progress mb-3 rounded-pill" style="height: 8px;">
                        <div class="progress-bar bg-success transition-all" id="progressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <button type="button" class="btn btn-success w-100 rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" onclick="confirmSubmitUjian(event);">
                        <i class="bi bi-check2-all fs-5"></i> Kirim Jawaban Sekarang
                    </button>
                </div>
            </div>

            <!-- Modal Navigasi Khusus HP/Mobile -->
            <div class="modal fade" id="mobileNavModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-light border-bottom pt-3 pb-3 px-4 rounded-top-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-grid-3x3-gap-fill text-primary fs-5"></i>
                                <h6 class="modal-title fw-bold text-dark mb-0">Daftar & Navigasi Soal</h6>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill small"><i class="bi bi-shield-check me-1"></i>Auto-Save</span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="small text-muted mb-3">Tekan pada nomor soal di bawah untuk beralih secara langsung:</p>
                            <div class="question-grid mb-4">
                                @foreach($materi->pretest_questions as $index => $question)
                                    <div class="nav-pill pill-{{ $index }} {{ $index === 0 ? 'active' : '' }}" onclick="navToQuestionFromModal({{ $index }})" title="Soal Nomor {{ $index + 1 }}">
                                        {{ $index + 1 }}
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1 small">
                                <span class="fw-bold text-muted">Progress Terjawab:</span>
                                <span class="badge bg-primary rounded-pill px-3" id="progressTextModal">0 / {{ $totalQuestions }}</span>
                            </div>
                            <div class="progress rounded-pill mb-4" style="height: 8px;">
                                <div class="progress-bar bg-success transition-all" id="progressBarModal" role="progressbar" style="width: 0%;"></div>
                            </div>

                            <button type="button" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-sm" onclick="confirmSubmitUjian(event);">
                                <i class="bi bi-send-check me-2"></i> Kirim & Selesaikan Ujian Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentIndex = 0;
        const totalQuestions = {{ $totalQuestions }};
        const answeredQuestions = new Set();
        const storageKey = "cbt_autosave_user_{{ Auth::id() }}_materi_{{ $materi->id }}";

        const Toast = Swal.mixin({
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        function navToQuestion(targetIndex) {
            document.querySelectorAll('.cbt-slide').forEach(slide => {
                slide.classList.add('d-none-custom');
            });
            
            document.querySelectorAll('.nav-pill').forEach(pill => {
                pill.classList.remove('active');
            });

            const targetSlide = document.getElementById('slide-' + targetIndex);
            if (targetSlide) {
                targetSlide.classList.remove('d-none-custom');
                targetSlide.querySelector('.worksheet-body')?.scrollTo({ top: 0, behavior: 'smooth' });
            }

            document.querySelectorAll('.pill-' + targetIndex).forEach(pill => {
                pill.classList.add('active');
            });

            currentIndex = targetIndex;
        }

        function navToQuestionFromModal(targetIndex) {
            navToQuestion(targetIndex);
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalEl = document.getElementById('mobileNavModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                }
            } catch (err) {
                console.warn(err);
            }
        }

        function showSaveIndicator() {
            const el = document.getElementById('autoSaveStatus');
            if (el) {
                el.innerHTML = '<i class="bi bi-cloud-check-fill text-white me-1"></i> Jawaban Tersimpan!';
                el.className = 'badge bg-success text-white px-3 py-1.5 rounded-pill text-nowrap ms-2 d-none d-md-inline-block shadow-sm';
                clearTimeout(el.timer);
                el.timer = setTimeout(() => {
                    el.innerHTML = '<i class="bi bi-shield-check text-success me-1"></i> Auto-Save Aktif';
                    el.className = 'badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-50 px-3 py-1.5 rounded-pill text-nowrap ms-2 d-none d-md-inline-block';
                }, 2500);
            }
        }

        function saveAnswerToLocal(qId, letter, idx) {
            let data = {};
            const existing = localStorage.getItem(storageKey);
            if (existing) {
                try { data = JSON.parse(existing); } catch(e) {}
            }
            data[qId] = { letter: letter, idx: idx };
            localStorage.setItem(storageKey, JSON.stringify(data));
            showSaveIndicator();
        }

        function restoreSavedAnswers() {
            const existing = localStorage.getItem(storageKey);
            if (!existing) return;
            try {
                const data = JSON.parse(existing);
                const keys = Object.keys(data);
                if (keys.length > 0) {
                    keys.forEach(qId => {
                        const item = data[qId];
                        const letter = item.letter;
                        const idx = item.idx;

                        const radio = document.querySelector('input[name="jawaban_' + qId + '"][value="' + letter + '"]');
                        if (radio) {
                            radio.checked = true;
                            applyVisualSelection(qId, letter, idx, false);
                        }
                    });
                    
                    Toast.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Auto-Save Berhasil Dipulihkan!',
                        text: keys.length + ' jawaban Anda sebelumnya telah dikembalikan otomatis.'
                    });
                }
            } catch(e) {
                console.error("Gagal memulihkan data auto-save:", e);
            }
        }

        function applyVisualSelection(qId, letter, idx, isUserClick = true) {
            document.querySelectorAll('input[name="jawaban_' + qId + '"]').forEach(r => {
                const card = r.closest('.option-item-card');
                card.classList.remove('selected');
                const checkInd = card.querySelector('.check-indicator');
                if (checkInd) checkInd.style.opacity = '0';
            });

            const activeCard = document.getElementById('card_' + qId + '_' + letter);
            if (activeCard) {
                activeCard.classList.add('selected');
                const checkInd = activeCard.querySelector('.check-indicator');
                if (checkInd) checkInd.style.opacity = '1';
            }

            const statusBadge = document.querySelector('.status-badge-' + qId);
            if (statusBadge) {
                statusBadge.className = 'badge bg-success text-white px-3 py-2 rounded-pill status-badge-' + qId;
                statusBadge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Terjawab';
            }

            answeredQuestions.add(idx);
            document.querySelectorAll('.pill-' + idx).forEach(pill => {
                pill.classList.add('answered');
            });

            updateProgress();
            if (isUserClick) {
                saveAnswerToLocal(qId, letter, idx);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('.option-radio');
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const qId = this.getAttribute('data-question-id');
                    const letter = this.getAttribute('data-letter');
                    const idx = parseInt(this.getAttribute('data-index'));

                    applyVisualSelection(qId, letter, idx, true);
                });
            });

            document.getElementById('spaPostTestForm').addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                }
            });

            restoreSavedAnswers();
        });

        function updateProgress() {
            const answeredCount = answeredQuestions.size;
            const progressPercent = Math.round((answeredCount / totalQuestions) * 100);
            const textStr = answeredCount + ' / ' + totalQuestions;
            
            const pText = document.getElementById('progressText');
            const pBar = document.getElementById('progressBar');
            if (pText) pText.innerText = textStr;
            if (pBar) pBar.style.width = progressPercent + '%';

            const pTextModal = document.getElementById('progressTextModal');
            const pBarModal = document.getElementById('progressBarModal');
            if (pTextModal) pTextModal.innerText = textStr;
            if (pBarModal) pBarModal.style.width = progressPercent + '%';

            document.querySelectorAll('.mobile-prog-text').forEach(el => {
                el.innerText = textStr;
            });
        }

        function confirmExitUjian(event, url) {
            event.preventDefault();
            Swal.fire({
                position: 'center',
                title: 'Keluar dari Ujian?',
                html: 'Jawaban Anda diamankan oleh <strong>Auto-Save</strong> di browser ini, namun <strong>belum dikirim ke server</strong>.<br><br><span class="text-muted small">Yakin ingin meninggalkan halaman ini sekarang?</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#2563eb',
                confirmButtonText: '<i class="bi bi-box-arrow-left me-1"></i> Ya, Keluar',
                cancelButtonText: 'Lanjut Ujian',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-4 fw-bold',
                    cancelButton: 'rounded-pill px-4 fw-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        function confirmSubmitUjian(event) {
            event.preventDefault();
            const answeredCount = answeredQuestions.size;
            
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalEl = document.getElementById('mobileNavModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                }
            } catch (err) {
                console.warn(err);
            }
            
            if (answeredCount < totalQuestions) {
                const sisa = totalQuestions - answeredCount;
                Swal.fire({
                    position: 'center',
                    title: 'Peringatan: Belum Selesai!',
                    html: `Masih terdapat <strong class="text-danger fs-4">${sisa} soal</strong> yang <strong>BELUM dijawab</strong>!<br><br><span class="text-muted small">Apakah Anda yakin tetap ingin mengakhiri dan mengirim ujian ini sekarang?</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bi bi-send-exclamation me-1"></i> Tetap Kirim',
                    cancelButtonText: 'Kembali Mengerjakan',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4 shadow-lg border-0',
                        confirmButton: 'rounded-pill px-4 fw-bold',
                        cancelButton: 'rounded-pill px-4 fw-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        doFinalSubmit();
                    }
                });
            } else {
                Swal.fire({
                    position: 'center',
                    title: 'Siap Mengirim Ujian?',
                    html: `Semua <strong>${totalQuestions} soal</strong> telah terjawab dengan lengkap!<br><br><span class="text-muted small">Setelah dikirim, nilai akan langsung dihitung dan Anda tidak dapat mengulang tes ini.</span>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bi bi-check2-circle me-1"></i> Ya, Kirim & Selesaikan!',
                    cancelButtonText: 'Periksa Ulang',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4 shadow-lg border-0',
                        confirmButton: 'rounded-pill px-4 fw-bold',
                        cancelButton: 'rounded-pill px-4 fw-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        doFinalSubmit();
                    }
                });
            }
        }

        function doFinalSubmit() {
            localStorage.removeItem(storageKey);
            
            Swal.fire({
                position: 'center',
                title: 'Mengirim Jawaban...',
                text: 'Mohon tunggu sebentar, sistem sedang memverifikasi dan mencatat nilai Anda.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Gunakan metode submit natif browser agar dijamin 100% tereksekusi
            const formEl = document.getElementById('spaPostTestForm');
            if (formEl) {
                HTMLFormElement.prototype.submit.call(formEl);
            }
        }
    </script>
</body>
</html>
