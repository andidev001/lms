@extends('layouts.guru')

@section('title', 'Kelola Tugas')
@section('subtitle', 'Pilih mata pelajaran untuk mengelola tugas')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($mapels as $mapel)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                <i class="bi bi-book fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $mapel->nama_mapel }}</h5>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border mt-1">Kode: {{ $mapel->kode_mapel }}</span>
                            </div>
                        </div>
                        
                        @if($mapel->kelas && $mapel->kelas->count() > 0)
                        <div class="mt-1 mb-4 d-flex flex-wrap gap-1">
                            @foreach($mapel->kelas as $kelas)
                                <span class="badge border text-dark bg-white shadow-sm" style="font-size: 0.7rem; font-weight: 500;">
                                    <i class="bi bi-door-open me-1 text-primary"></i> {{ $kelas->nama_kelas }}
                                </span>
                            @endforeach
                        </div>
                        @else
                        <div class="mt-1 mb-4">
                            <span class="badge bg-light text-muted border w-100 text-start" style="font-size: 0.7rem; font-weight: normal;">
                                <i class="bi bi-info-circle me-1"></i> Belum terhubung ke kelas
                            </span>
                        </div>
                        @endif
                        
                        <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <i class="bi bi-journal-text me-1"></i> {{ $mapel->tugas()->count() }} Tugas
                            </span>
                            <a href="{{ route('guru.mapels.tugas.show', $mapel->id) }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                Kelola Tugas <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/amber/student-going-to-school.svg" alt="Empty" style="height: 200px;" class="mb-4 opacity-50">
                    <h5 class="fw-bold text-muted">Belum ada mata pelajaran</h5>
                    <p class="text-muted mb-0">Anda belum ditugaskan untuk mengajar mata pelajaran apapun.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
