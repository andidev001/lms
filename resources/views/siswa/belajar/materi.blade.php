@extends('layouts.siswa')

@section('title', 'Aktivitas Belajar')
@section('subtitle', $mapel->nama_mapel . ' - ' . $materi->judul)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('siswa.mapels.show', ['mapel' => $mapel->id, 'materi_id' => $materi->id]) }}" class="btn btn-light rounded-pill px-4 border shadow-sm fw-semibold">
        <i class="bi bi-arrow-left me-2"></i> Kembali ke Modul
    </a>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-10">
        
        @if(request('view') == 'pdf' && $materi->file_pdf)
            <!-- Mode PDF -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-pdf text-danger me-2"></i> Dokumen Materi</h5>
                </div>
                <div class="card-body p-0">
                    <iframe src="{{ asset('storage/' . $materi->file_pdf) }}" width="100%" height="600px" style="border:none;"></iframe>
                </div>
                <div class="card-footer bg-light border-top-0 p-4">
                    <form action="{{ route('siswa.mapels.materis.mark', [$mapel->id, $materi->id]) }}" method="POST" class="text-end">
                        @csrf
                        <input type="hidden" name="pdf_dibaca" value="1">
                        @if($progress && $progress->pdf_dibaca)
                            <button type="button" class="btn btn-success rounded-pill px-5 fw-semibold" disabled>
                                <i class="bi bi-check-circle-fill me-2"></i> Selesai Dibaca
                            </button>
                        @else
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-semibold shadow-sm">
                                <i class="bi bi-check2-all me-2"></i> Tandai Sudah Dibaca
                            </button>
                        @endif
                    </form>
                </div>
            </div>
            
        @elseif(request('view') == 'video' && $materi->url_youtube)
            <!-- Mode Video -->
            @php
                $videoId = '';
                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $materi->url_youtube, $match)) {
                    $videoId = $match[1];
                }
            @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-youtube text-danger me-2"></i> Video Pembelajaran</h5>
                </div>
                <div class="card-body p-0 bg-dark position-relative" style="padding-top: 56.25%;">
                    @if($videoId)
                        <iframe class="position-absolute top-0 start-0 w-100 h-100" src="https://www.youtube.com/embed/{{ $videoId }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    @else
                        <div class="position-absolute top-50 start-50 translate-middle text-white text-center">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-2"></i>
                            <p>Format URL Youtube tidak valid.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light border-top-0 p-4">
                    <form action="{{ route('siswa.mapels.materis.mark', [$mapel->id, $materi->id]) }}" method="POST" class="text-end">
                        @csrf
                        <input type="hidden" name="video_ditonton" value="1">
                        @if($progress && $progress->video_ditonton)
                            <button type="button" class="btn btn-success rounded-pill px-5 fw-semibold" disabled>
                                <i class="bi bi-check-circle-fill me-2"></i> Selesai Ditonton
                            </button>
                        @else
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-semibold shadow-sm">
                                <i class="bi bi-check2-all me-2"></i> Tandai Sudah Ditonton
                            </button>
                        @endif
                    </form>
                </div>
            </div>
            
        @else
            <!-- Fallback if view parameter is missing but user reaches here -->
            <div class="alert alert-warning rounded-4 p-4 shadow-sm text-center">
                <i class="bi bi-exclamation-triangle-fill fs-1 text-warning mb-3 d-block"></i>
                <h5 class="fw-bold">Aktivitas tidak ditemukan</h5>
                <p class="mb-0">Silakan kembali ke modul dan pilih aktivitas dengan benar.</p>
            </div>
        @endif

    </div>
</div>
@endsection
