@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')
@section('subtitle', 'Terus kembangkan potensimu!')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-muted fw-normal mb-0">Mata Pelajaran</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-journal-bookmark fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalMapel }}</h3>
                <p class="text-muted small mb-0">Terdaftar semester ini</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-muted fw-normal mb-0">Modul Selesai</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $modulSelesai }}</h3>
                <p class="text-success small mb-0"><i class="bi bi-stars me-1"></i> Terus tingkatkan!</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-muted fw-normal mb-0">Nilai Rata-rata Post-Test</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-star-half fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $nilaiRataRata }}</h3>
                <p class="text-muted small mb-0">Rata-rata evaluasi Anda</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom border-light pt-4 pb-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-megaphone fs-4 text-dark"></i>
                    <h5 class="fw-bold mb-0 text-dark">Pengumuman</h5>
                </div>
                <a href="{{ route('siswa.dashboard') }}" class="btn btn-info text-white d-flex align-items-center gap-1 py-1 px-3" style="background-color: #0ea5e9; border: none; border-radius: 4px;">
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
                    
                    @forelse($pengumumans ?? [] as $p)
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
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted position-relative z-index-2">
                        <i class="bi bi-inbox fs-2 mb-2 d-block"></i>
                        <p class="mb-0 small">Belum ada pengumuman.</p>
                    </div>
                    @endforelse
                    
                    @if(isset($pengumumans) && $pengumumans->count() > 0)
                    <!-- End clock icon -->
                    <div class="timeline-end bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center border border-white border-3 shadow-sm" style="position: absolute; left: 0; bottom: -10px; width: 32px; height: 32px; z-index: 2; transform: translateX(-50%);">
                        <i class="bi bi-clock small"></i>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom border-light pt-4 pb-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Pelajaran Anda</h5>
                    <a href="{{ route('siswa.belajar') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-4">
                @forelse($mapels->take(5) as $mapel)
                <div class="d-flex align-items-center p-3 mb-3 border rounded-3 bg-light bg-opacity-50 hover-shadow transition-all">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <span class="fw-bold">{{ substr($mapel->nama_mapel, 0, 1) }}</span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">{{ $mapel->nama_mapel }}</h6>
                        <p class="text-muted small mb-0">Guru: <span class="fw-semibold">{{ $mapel->guru_pengampu->nama ?? 'Belum ditentukan' }}</span></p>
                    </div>
                    <div>
                        <a href="{{ route('siswa.mapels.show', $mapel->id) }}" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm">Mulai Belajar</a>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-journal-x text-muted fs-1"></i>
                    </div>
                    <p class="text-muted mb-0">Belum ada mata pelajaran terdaftar.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endsection
