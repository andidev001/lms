@extends('layouts.guru')

@section('title', 'Rekap Nilai & Progress')
@section('subtitle', 'Pilih Kelas untuk melihat progress belajar siswa')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-collection me-2 text-primary"></i> Daftar Mata Pelajaran Anda</h5>
                
                @if($mapels->count() > 0)
                    <div class="accordion accordion-flush border rounded-4 overflow-hidden" id="accordionMapels">
                        @foreach($mapels as $index => $mapel)
                            <div class="accordion-item {{ !$loop->last ? 'border-bottom' : '' }}">
                                <h2 class="accordion-header" id="heading-{{ $mapel->id }}">
                                    <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} bg-light fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $mapel->id }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $mapel->id }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-book-half"></i>
                                            </div>
                                            <div>
                                                <div class="mb-1 text-dark">{{ $mapel->nama_mapel }}</div>
                                                <div class="small fw-normal text-muted"><i class="bi bi-layers me-1"></i> {{ $mapel->kelas->count() }} Kelas terhubung</div>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $mapel->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading-{{ $mapel->id }}" data-bs-parent="#accordionMapels">
                                    <div class="accordion-body p-4 bg-white">
                                        @if($mapel->kelas->count() > 0)
                                            <div class="row g-3">
                                                @foreach($mapel->kelas as $kelas)
                                                <div class="col-md-6 col-lg-4">
                                                    <a href="{{ route('guru.progress.show', [$mapel->id, $kelas->id]) }}" class="text-decoration-none">
                                                        <div class="card border border-primary border-opacity-25 h-100 hover-shadow transition-all bg-primary bg-opacity-10 rounded-3">
                                                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <h6 class="fw-bold text-dark mb-1">{{ $kelas->nama_kelas }}</h6>
                                                                    <span class="text-muted small">Lihat Rekap Nilai</span>
                                                                </div>
                                                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                                                    <i class="bi bi-chevron-right text-primary"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-light text-center mb-0 rounded-3 border">
                                                <i class="bi bi-exclamation-circle text-muted mb-2 d-block fs-4"></i>
                                                Mata pelajaran ini belum dihubungkan ke kelas manapun.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info border-0 rounded-4 p-4 text-center">
                        <i class="bi bi-info-circle-fill fs-3 mb-3 d-block"></i>
                        <h5>Belum ada Mata Pelajaran</h5>
                        <p class="mb-0">Anda belum ditugaskan untuk mengampu mata pelajaran apapun.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08)!important;
    transform: translateY(-2px);
}
.transition-all {
    transition: all 0.3s ease;
}
.accordion-button:not(.collapsed) {
    color: var(--bs-dark);
    box-shadow: inset 0 calc(-1 * var(--bs-accordion-border-width)) 0 var(--bs-accordion-border-color);
}
</style>
@endsection
