@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Ikhtisar platform pembelajaran Anda')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-muted fw-normal mb-0">Total Siswa</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($totalSiswa) }}</h3>
                <p class="text-success small mb-0"><i class="bi bi-arrow-up-short"></i> Total terdaftar</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-muted fw-normal mb-0">Total Guru</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-workspace fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($totalGuru) }}</h3>
                <p class="text-muted small mb-0">Total pengajar</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-muted fw-normal mb-0">Total Kelas</h6>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($totalKelas) }}</h3>
                <p class="text-success small mb-0"><i class="bi bi-arrow-up-short"></i> Kelas terdaftar</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-muted fw-normal mb-0">Mapel Tersedia</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-book fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($totalMapel) }}</h3>
                <p class="text-success small mb-0"><i class="bi bi-arrow-up-short"></i> Mata pelajaran</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom border-light pt-4 pb-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-megaphone fs-4 text-dark"></i>
                    <h5 class="fw-bold mb-0 text-dark">Pengumuman</h5>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-info text-white d-flex align-items-center gap-1 py-1 px-3" style="background-color: #0ea5e9; border: none; border-radius: 4px;">
                    <i class="bi bi-arrow-repeat"></i> Refresh
                </a>
            </div>
            <div class="card-body p-4 pt-4">
                <div class="mb-4">
                    <span class="badge bg-success px-3 py-2 text-white shadow-sm" style="font-size: 0.85rem; border-radius: 6px;">Pengumuman Terakhir</span>
                </div>
                
                <div class="timeline" style="position: relative; margin-left: 1rem; padding-left: 2rem; padding-bottom: 1.5rem;">
                    <!-- Vertical Line -->
                    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background-color: #e2e8f0; z-index: 1; transform: translateX(-50%);"></div>
                    
                    @forelse($pengumumans as $p)
                    <div class="timeline-item mb-4" style="position: relative; z-index: 2;">
                        <!-- Icon -->
                        <div class="timeline-icon text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-white border-3" style="position: absolute; left: -2rem; top: 0; width: 36px; height: 36px; background-color: #0ea5e9; transform: translateX(-50%); z-index: 3;">
                            <i class="bi bi-envelope"></i>
                        </div>
                        
                        <!-- Content Card -->
                        <div class="card border border-light shadow-sm rounded-2">
                            <div class="card-body p-3 px-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between mb-2 pb-2 border-bottom border-light">
                                    <div>
                                        <h6 class="mb-1 fw-semibold" style="color: #0284c7; font-size: 0.95rem;">{{ $p->judul }}</h6>
                                        <div class="text-muted" style="font-size: 0.8rem;">{{ $p->user->name ?? 'Admin' }}</div>
                                    </div>
                                    <div class="text-muted mt-2 mt-md-0 d-flex align-items-center gap-3" style="font-size: 0.8rem;">
                                        <span><i class="bi bi-calendar me-1"></i> {{ $p->created_at->format('d-m-Y') }}</span>
                                        <span><i class="bi bi-clock me-1"></i> {{ $p->created_at->format('H:i') }}</span>
                                    </div>
                                </div>
                                <div class="text-dark mb-2" style="font-size: 0.85rem; line-height: 1.6;">
                                    {!! nl2br(e($p->konten)) !!}
                                </div>
                                @if($p->target != 'semua')
                                <div class="mt-2">
                                    <span class="badge bg-light text-dark border" style="font-size: 0.75rem;"><i class="bi bi-tag me-1"></i> Target: {{ ucfirst($p->target) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted position-relative z-index-2">
                        <i class="bi bi-inbox fs-2 mb-2 d-block"></i>
                        <p class="mb-0 small">Belum ada pengumuman.</p>
                    </div>
                    @endforelse
                    
                    @if($pengumumans->count() > 0)
                    <!-- End clock icon -->
                    <div class="timeline-end bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center border border-white border-3 shadow-sm" style="position: absolute; left: 0; bottom: -10px; width: 32px; height: 32px; z-index: 2; transform: translateX(-50%);">
                        <i class="bi bi-clock small"></i>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom border-light pt-4 pb-3 px-4 d-flex align-items-center gap-2">
                <div class="bg-warning bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-lightning-charge text-warning"></i>
                </div>
                <h5 class="fw-bold mb-0 text-dark">Tugas Cepat</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-3">
                    <a href="{{ url('admin/users') }}" class="text-decoration-none">
                        <button class="btn btn-light text-start p-3 d-flex align-items-center rounded-3 border w-100 table-hover-btn">
                            <i class="bi bi-people text-primary fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-semibold text-dark">Kelola Pengguna</h6>
                                <small class="text-muted">Data Guru & Siswa</small>
                            </div>
                        </button>
                    </a>
                    
                    <a href="{{ url('admin/kelas') }}" class="text-decoration-none">
                        <button class="btn btn-light text-start p-3 d-flex align-items-center rounded-3 border w-100 table-hover-btn">
                            <i class="bi bi-building text-warning fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-semibold text-dark">Manajemen Kelas</h6>
                                <small class="text-muted">Atur rombongan belajar</small>
                            </div>
                        </button>
                    </a>
                    
                    <a href="{{ url('admin/mapels') }}" class="text-decoration-none">
                        <button class="btn btn-light text-start p-3 d-flex align-items-center rounded-3 border w-100 table-hover-btn">
                            <i class="bi bi-journal-bookmark text-success fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-semibold text-dark">Mata Pelajaran</h6>
                                <small class="text-muted">Kelola kurikulum mapel</small>
                            </div>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
