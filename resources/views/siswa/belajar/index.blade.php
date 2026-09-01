@extends('layouts.siswa')

@section('title', 'Ruang Belajar')
@section('subtitle', 'Pilih Mata Pelajaran untuk mulai belajar')

@section('content')
@if (isset($error))
    <div class="alert alert-warning border-warning border-opacity-25 bg-warning bg-opacity-10 rounded-4 p-4 shadow-sm">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill text-warning fs-3 me-3"></i>
            <div>
                <h6 class="fw-bold mb-1">Perhatian</h6>
                <p class="mb-0">{{ $error }}</p>
            </div>
        </div>
    </div>
@else
    <div class="row g-4">
        @forelse($mapels as $mapel)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">{{ $mapel->kode_mapel }}</span>
                            @if($mapel->kategori)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">{{ $mapel->kategori }}</span>
                            @endif
                        </div>
                        
                        <h5 class="fw-bold text-dark mb-1">{{ $mapel->nama_mapel }}</h5>
                        <p class="text-muted small mb-4">Guru: <span class="fw-semibold">{{ $mapel->guru_pengampu->nama ?? 'Belum ditentukan' }}</span></p>
                        
                        <div class="d-flex align-items-center justify-content-between mb-3 bg-light p-2 rounded-3">
                            <span class="text-muted small"><i class="bi bi-journal-text me-1"></i> {{ $mapel->materis()->count() }} Materi</span>
                            <!-- Opsional: Progress Bar bisa ditambahkan di sini kedepannya -->
                        </div>

                        <a href="{{ route('siswa.mapels.show', $mapel->id) }}" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                            Mulai Belajar <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <div class="py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-journal-x text-muted fs-1"></i>
                        </div>
                        <h4 class="fw-bold">Belum Ada Pelajaran</h4>
                        <p class="text-muted mb-0">Tidak ada mata pelajaran yang tersedia untuk kelas Anda saat ini.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endif

<style>
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection
