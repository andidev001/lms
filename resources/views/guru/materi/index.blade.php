@extends('layouts.guru')

@section('title', 'Kelola Materi')
@section('subtitle', 'Mata Pelajaran: ' . $mapel->nama_mapel)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('guru.dashboard') }}" class="btn btn-light rounded-pill px-3 border shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
    </a>
    <a href="{{ route('guru.mapels.materis.create', $mapel->id) }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-circle me-2"></i> Tambah Materi Baru
    </a>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    @forelse ($materis as $materi)
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="row g-0">
                    <div class="col-md-1 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center p-3 border-end">
                        <div class="text-center">
                            <span class="d-block text-muted small fw-bold text-uppercase">Modul</span>
                            <span class="display-6 fw-bold text-primary">{{ $materi->urutan }}</span>
                        </div>
                    </div>
                    <div class="col-md-8 p-4">
                        <h4 class="fw-bold mb-2">{{ $materi->judul }}</h4>
                        <p class="text-muted mb-3">{{ Str::limit(strip_tags($materi->deskripsi), 150) }}</p>
                        
                        <div class="d-flex flex-wrap gap-2 mb-0">
                            @if($materi->file_pdf)
                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalPdf{{ $materi->id }}" class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 text-decoration-none shadow-sm" style="cursor: pointer; transition: all 0.2s;">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF Tersedia <i class="bi bi-eye ms-1"></i>
                                </a>
                            @endif
                            @if($materi->url_youtube)
                                <span class="badge bg-danger text-white rounded-pill px-3 py-2">
                                    <i class="bi bi-youtube me-1"></i> Video Tersedia
                                </span>
                            @endif
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2">
                                <i class="bi bi-question-circle-fill me-1"></i> {{ $materi->pretest_questions()->count() }} Soal Post-Test
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3 bg-light p-4 d-flex flex-column justify-content-center align-items-end border-start">
                        <a href="{{ route('guru.mapels.materis.show', [$mapel->id, $materi->id]) }}" class="btn btn-primary w-100 mb-2 rounded-3">
                            <i class="bi bi-gear-fill me-1"></i> Kelola & Soal
                        </a>
                        <a href="{{ route('guru.mapels.materis.edit', [$mapel->id, $materi->id]) }}" class="btn btn-outline-secondary w-100 mb-2 rounded-3">
                            <i class="bi bi-pencil-square me-1"></i> Edit Detail
                        </a>
                        <form action="{{ route('guru.mapels.materis.destroy', [$mapel->id, $materi->id]) }}" method="POST" class="w-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-3" onclick="return confirm('Yakin ingin menghapus materi ini beserta soal-soalnya? (Data progres siswa terkait juga akan terhapus)')">
                                <i class="bi bi-trash me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if($materi->file_pdf)
            <!-- Modal PDF Preview -->
            <div class="modal fade" id="modalPdf{{ $materi->id }}" tabindex="-1" aria-labelledby="modalPdfLabel{{ $materi->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                            <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" id="modalPdfLabel{{ $materi->id }}">
                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-4 me-2"></i> Preview PDF: {{ $materi->judul }}
                            </h5>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ asset('storage/' . $materi->file_pdf) }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Tab Baru
                                </a>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body p-0 bg-secondary bg-opacity-10" style="height: 80vh;">
                            <iframe src="{{ asset('storage/' . $materi->file_pdf) }}" width="100%" height="100%" style="border: none; min-height: 500px;">
                                Browser Anda tidak mendukung iframe. <a href="{{ asset('storage/' . $materi->file_pdf) }}" target="_blank">Download PDF di sini</a>.
                            </iframe>
                        </div>
                        <div class="modal-footer bg-light border-0 py-2 px-4 justify-content-end">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                <div class="py-5">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-journal-x text-muted fs-1"></i>
                    </div>
                    <h4 class="fw-bold">Belum Ada Materi</h4>
                    <p class="text-muted mb-4">Anda belum menambahkan materi apapun untuk mata pelajaran ini.</p>
                    <a href="{{ route('guru.mapels.materis.create', $mapel->id) }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i> Tambah Materi Pertama
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
