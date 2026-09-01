@extends('layouts.guru')

@section('title', 'Dashboard')
@section('subtitle', 'Ikhtisar aktivitas mengajar Anda')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-muted fw-normal mb-0">Mapel Diampu</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-book fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ isset($mapels) ? $mapels->count() : 0 }}</h3>
                <p class="text-muted small mb-0">Mata Pelajaran</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-muted fw-normal mb-0">Total Materi</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($totalMateri ?? 0) }}</h3>
                <p class="text-muted small mb-0">Total materi yang diunggah</p>
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
                <a href="{{ route('guru.dashboard') }}" class="btn btn-info text-white d-flex align-items-center gap-1 py-1 px-3" style="background-color: #0ea5e9; border: none; border-radius: 4px;">
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
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom border-light pt-4 pb-3 px-4">
                <h5 class="fw-bold mb-0 text-dark">Mata Pelajaran Anda</h5>
            </div>
            <div class="card-body p-4">
                @if(isset($mapels) && $mapels->count() > 0)
                    <div class="row g-3">
                        @foreach($mapels as $mapel)
                            <div class="col-md-6">
                                <div class="card h-100 border rounded-3 bg-light bg-opacity-50 hover-shadow transition-all">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $mapel->kode_mapel }}</span>
                                            @if($mapel->kategori)
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $mapel->kategori }}</span>
                                            @endif
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $mapel->nama_mapel }}</h6>
                                        <p class="text-muted small mb-2 text-truncate" style="min-height: 20px;">{{ $mapel->keterangan ?? 'Tidak ada deskripsi' }}</p>
                                        
                                        @if($mapel->kelas && $mapel->kelas->count() > 0)
                                        <div class="mb-3 d-flex flex-wrap gap-1">
                                            @foreach($mapel->kelas as $kelas)
                                                <span class="badge border text-dark bg-white shadow-sm" style="font-size: 0.7rem; font-weight: 500;">
                                                    <i class="bi bi-door-open me-1 text-primary"></i> {{ $kelas->nama_kelas }}
                                                </span>
                                            @endforeach
                                        </div>
                                        @else
                                        <div class="mb-3">
                                            <span class="badge bg-light text-muted border w-100 text-start" style="font-size: 0.7rem; font-weight: normal;">
                                                <i class="bi bi-info-circle me-1"></i> Belum terhubung ke kelas manapun
                                            </span>
                                        </div>
                                        @endif
                                        
                                        <a href="{{ route('guru.mapels.materis.index', $mapel->id) }}" class="btn btn-sm btn-primary w-100 rounded-pill">Kelola Materi <i class="bi bi-arrow-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-journal-x fs-1 text-muted"></i>
                        </div>
                        <h6 class="fw-bold text-dark">Belum Ada Mata Pelajaran</h6>
                        <p class="text-muted small mb-0">Anda belum ditugaskan untuk mengajar mata pelajaran apapun.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom border-light pt-4 pb-3 px-4">
                <h5 class="fw-bold mb-0 text-dark">Aksi Cepat</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-3">
                    <div class="dropdown">
                        <button class="btn btn-light text-start p-3 d-flex align-items-center rounded-3 border w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-cloud-arrow-up text-primary fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-semibold text-dark">Upload Materi</h6>
                                <small class="text-muted">Untuk bahan ajar</small>
                            </div>
                        </button>
                        <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1">
                            @forelse($mapels ?? [] as $mapel)
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('guru.mapels.materis.index', $mapel->id) }}">
                                        <i class="bi bi-book text-primary opacity-75"></i> {{ $mapel->nama_mapel }}
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item text-muted">Belum ada mapel</span></li>
                            @endforelse
                        </ul>
                    </div>
                    
                    <a href="{{ route('guru.tugas.index') }}" class="text-decoration-none">
                        <button class="btn btn-light text-start p-3 d-flex align-items-center rounded-3 border w-100">
                            <i class="bi bi-ui-checks-grid text-success fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-semibold text-dark">Buat Post-test</h6>
                                <small class="text-muted">Evaluasi awal siswa</small>
                            </div>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.hover-shadow:hover {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    background-color: #fff !important;
}
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection
