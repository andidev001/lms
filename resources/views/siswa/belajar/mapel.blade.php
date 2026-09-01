@extends('layouts.siswa')

@section('title', 'Ruang Belajar')
@section('subtitle', 'Topik: ' . $mapel->nama_mapel)

@section('content')
<div class="row g-4 mb-4">
    <!-- Kolom Kiri: Navigasi Modul -->
    <div class="col-lg-4">
        <!-- Info Topik -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <span class="text-muted small fw-bold text-uppercase mb-2 d-block">TOPIK</span>
                <h5 class="fw-bold text-dark mb-3">{{ $mapel->nama_mapel }}</h5>
                <h6 class="fw-bold mb-2 text-dark">Yang akan dipelajari</h6>
                <div class="text-muted small">
                    {!! nl2br(e($mapel->keterangan ?? 'Topik ini memuat penjelasan tentang materi yang relevan secara adaptif dalam kegiatan pembelajaran di kelas.')) !!}
                </div>
            </div>
        </div>

        <!-- Daftar Modul Pelatihan -->
        <h6 class="fw-bold mb-3 text-dark">Modul Pelatihan</h6>
        <p class="text-muted small mb-3">Pelajari modul sesuai urutan.</p>

        @forelse($materis as $index => $materi)
            @php
                $isActive = $activeMateri && $activeMateri->id == $materi->id;
                $isCompleted = isset($progress[$materi->id]) && $progress[$materi->id]->is_completed;
                $isLocked = false;
                
                // Cek sequential (terkunci jika modul sebelumnya belum selesai)
                if ($index > 0) {
                    $prevMateriId = $materis[$index - 1]->id;
                    $isLocked = !isset($progress[$prevMateriId]) || !$progress[$prevMateriId]->is_completed;
                }
            @endphp
            <div class="card border {{ $isActive ? 'border-primary shadow-sm bg-primary bg-opacity-10' : 'border-light shadow-sm mb-3' }} rounded-4 mb-3 transition-all hover-shadow">
                <div class="card-body p-4 position-relative">
                    @if($isLocked)
                        <div class="position-absolute top-0 end-0 p-3 text-muted">
                            <i class="bi bi-lock-fill fs-5"></i>
                        </div>
                    @endif
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 {{ $isCompleted ? 'bg-success' : ($isLocked ? 'bg-secondary' : 'bg-primary') }}" style="width: 35px; height: 35px;">
                            @if($isCompleted)
                                <i class="bi bi-check-lg fs-5"></i>
                            @else
                                <span class="fw-bold">{{ $materi->urutan }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Modul {{ $materi->urutan }}</span>
                            <h6 class="fw-bold mb-2 {{ $isLocked ? 'text-muted' : 'text-dark' }}">{{ $materi->judul }}</h6>
                            <div class="d-flex align-items-center gap-3 text-muted small mb-3">
                                @if($materi->url_youtube)
                                <span><i class="bi bi-play-circle me-1"></i> Video</span>
                                @endif
                                @if($materi->file_pdf)
                                <span><i class="bi bi-file-earmark-pdf me-1"></i> Materi</span>
                                @endif
                            </div>
                            
                            @if($isLocked)
                                <button class="btn btn-sm btn-secondary rounded-pill px-3 py-1 fw-semibold disabled" style="font-size: 0.8rem;">Terkunci</button>
                            @else
                                <a href="{{ route('siswa.mapels.show', ['mapel' => $mapel->id, 'materi_id' => $materi->id]) }}" class="btn btn-sm {{ $isActive ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
                                    {{ $isCompleted ? 'Pelajari Ulang' : 'Pelajari Materi' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info border-0 rounded-4 shadow-sm p-4 text-center">
                Belum ada modul pelatihan.
            </div>
        @endforelse
    </div>

    <!-- Kolom Kanan: Detail Modul -->
    <div class="col-lg-8">
        @if($activeMateri)
            @php
                $activeProgress = $progress[$activeMateri->id] ?? null;
                $hasPretest = $activeMateri->pretest_questions()->count() > 0;
                
                $needsPdf = $activeMateri->file_pdf ? !($activeProgress && $activeProgress->pdf_dibaca) : false;
                $needsVideo = $activeMateri->url_youtube ? !($activeProgress && $activeProgress->video_ditonton) : false;
                $needsRefleksi = !($activeProgress && !empty($activeProgress->cerita_reflektif));
                
                $canTakePretest = !$needsPdf && !$needsVideo && !$needsRefleksi;
            @endphp

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if ($message = Session::get('info'))
                <div class="alert alert-info alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <span class="text-muted small fw-bold text-uppercase mb-2 d-block">MODUL</span>
                    <h4 class="fw-bold text-dark mb-3">{{ $activeMateri->judul }}</h4>
                    <div class="d-flex align-items-center mb-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                            <i class="bi bi-building" style="font-size: 0.7rem;"></i>
                        </div>
                        <span class="text-muted small">Disusun oleh {{ $mapel->guru_pengampu->nama ?? 'Guru ' . $mapel->nama_mapel }}</span>
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" class="btn btn-outline-dark bg-transparent rounded-3 fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modulDetailModal" style="border-color: #d1d5db;">
                            Baca detail modul
                        </button>
                    </div>

                    <!-- Modal Detail Modul -->
                    <div class="modal fade" id="modulDetailModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-body p-4 p-md-5">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div>
                                            <span class="text-muted small fw-bold text-uppercase mb-2 d-block">MODUL</span>
                                            <h4 class="fw-bold text-dark">{{ $activeMateri->judul }}</h4>
                                        </div>
                                        <button type="button" class="btn-close mt-2" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    @if($activeMateri->deskripsi)
                                        <div class="text-dark mb-4" style="line-height: 1.6; font-size: 1.05rem;">{!! $activeMateri->deskripsi !!}</div>
                                    @else
                                        <p class="text-muted fst-italic mb-4">Tidak ada deskripsi untuk modul ini.</p>
                                    @endif

                                    <h5 class="fw-bold mt-2 mb-3 fs-6">Daftar Materi</h5>
                                    <ul class="text-dark mb-4" style="line-height: 1.6; font-size: 1.05rem;">
                                        <li>Memahami {{ $activeMateri->judul }}</li>
                                    </ul>

                                    <h5 class="fw-bold mt-2 mb-3 fs-6">Langkah Penyelesaian Modul</h5>
                                    <ol class="text-dark mb-2" style="line-height: 1.6; font-size: 1.05rem;">
                                        <li>Belajar materi</li>
                                        <li>Refleksi pembelajaran</li>
                                        <li>Post Test</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accordion Daftar Materi -->
            <div class="card border shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 p-4">
                    <h5 class="fw-bold mb-0">Daftar Materi</h5>
                </div>
                
                <div class="accordion accordion-flush" id="accordionMateri">
                    <div class="accordion-item border-top border-bottom-0">
                        <h2 class="accordion-header" id="headingAktivitas">
                            <button class="accordion-button bg-light fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAktivitas" aria-expanded="true" aria-controls="collapseAktivitas">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $canTakePretest ? 'bg-success text-white' : 'bg-secondary bg-opacity-25 text-secondary' }}" style="width: 24px; height: 24px;">
                                        <i class="bi bi-check-lg" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <span>Memahami {{ $activeMateri->judul }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseAktivitas" class="accordion-collapse collapse show" aria-labelledby="headingAktivitas">
                            <div class="accordion-body p-4 pt-3">
                                
                                <span class="text-muted small fw-bold text-uppercase mb-3 d-block" style="letter-spacing: 1px;">AKTIVITAS</span>
                                
                                <ul class="list-unstyled mb-4 ps-2">
                                    @if($activeMateri->url_youtube)
                                    <li class="mb-3 d-flex align-items-start gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mt-1 flex-shrink-0 {{ ($activeProgress && $activeProgress->video_ditonton) ? 'bg-success text-white' : 'bg-secondary bg-opacity-25 text-secondary' }}" style="width: 20px; height: 20px;">
                                            <i class="bi bi-check-lg" style="font-size: 0.7rem;"></i>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-play-btn-fill fs-5 text-dark"></i>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#videoModal" class="text-primary text-decoration-none fw-semibold hover-underline">Video Pembelajaran</a>
                                        </div>
                                    </li>
                                    
                                    <!-- Modal Video -->
                                    <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-play-btn-fill me-2 text-danger"></i>Video Pembelajaran</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-0">
                                                    @php
                                                        $videoId = '';
                                                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $activeMateri->url_youtube, $match)) {
                                                            $videoId = $match[1];
                                                        }
                                                    @endphp
                                                    @if($videoId)
                                                        <div class="ratio ratio-16x9">
                                                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
                                                        </div>
                                                    @else
                                                        <div class="p-5 text-center text-muted">URL Youtube tidak valid.</div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer justify-content-between">
                                                    <span class="text-muted small">Tonton video hingga selesai agar Anda memahami materi.</span>
                                                    @if(!($activeProgress && $activeProgress->video_ditonton))
                                                    <form action="{{ route('siswa.mapels.materis.mark', [$mapel->id, $activeMateri->id]) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="type" value="video">
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                                                            <i class="bi bi-check2-circle me-1"></i> Tandai Sudah Ditonton
                                                        </button>
                                                    </form>
                                                    @else
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Selesai Ditonton</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($activeMateri->file_pdf)
                                    <li class="mb-3 d-flex align-items-start gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mt-1 flex-shrink-0 {{ ($activeProgress && $activeProgress->pdf_dibaca) ? 'bg-success text-white' : 'bg-secondary bg-opacity-25 text-secondary' }}" style="width: 20px; height: 20px;">
                                            <i class="bi bi-check-lg" style="font-size: 0.7rem;"></i>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-pdf-fill fs-5 text-dark"></i>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#pdfModal" class="text-primary text-decoration-none fw-semibold hover-underline">Dokumen Materi</a>
                                        </div>
                                    </li>

                                    <!-- Modal PDF -->
                                    <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-pdf-fill me-2 text-danger"></i>Dokumen Materi (PDF)</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-0" style="height: 75vh;">
                                                    <iframe src="{{ asset('storage/' . $activeMateri->file_pdf) }}" width="100%" height="100%" style="border: none;"></iframe>
                                                </div>
                                                <div class="modal-footer justify-content-between">
                                                    <span class="text-muted small">Baca dan pahami dokumen materi di atas.</span>
                                                    @if(!($activeProgress && $activeProgress->pdf_dibaca))
                                                    <form action="{{ route('siswa.mapels.materis.mark', [$mapel->id, $activeMateri->id]) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="type" value="pdf">
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                                                            <i class="bi bi-check2-circle me-1"></i> Tandai Sudah Dibaca
                                                        </button>
                                                    </form>
                                                    @else
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Selesai Dibaca</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </ul>

                                <hr class="border-light opacity-50 mb-4">

                                <span class="text-muted small fw-bold text-uppercase mb-3 d-block" style="letter-spacing: 1px;">CERITA REFLEKTIF</span>
                                
                                <div class="d-flex align-items-start gap-3 mb-2 ps-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mt-2 flex-shrink-0 {{ ($activeProgress && !empty($activeProgress->cerita_reflektif)) ? 'bg-success text-white' : 'bg-secondary bg-opacity-25 text-secondary' }}" style="width: 20px; height: 20px;">
                                        <i class="bi bi-check-lg" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        @if($activeProgress && !empty($activeProgress->cerita_reflektif))
                                            <div class="bg-light bg-opacity-50 p-3 rounded-3 border">
                                                <p class="mb-0 text-dark">{{ $activeProgress->cerita_reflektif }}</p>
                                            </div>
                                        @else
                                            <form action="{{ route('siswa.mapels.materis.mark', [$mapel->id, $activeMateri->id]) }}" method="POST">
                                                @csrf
                                                <div class="input-group">
                                                    <input type="text" name="cerita_reflektif" class="form-control form-control-lg bg-light border-0" placeholder="Tulis refleksi Anda disini..." required style="font-size: 0.95rem;">
                                                    <button class="btn btn-light border px-4" type="submit">Simpan</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Post Test -->
            @if($hasPretest)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                <div class="card-header bg-transparent border-bottom-0 p-4 pb-0">
                    <h6 class="fw-bold mb-0">Post Test</h6>
                </div>
                <div class="card-body p-4">
                    <div class="border rounded-4 p-4 d-flex justify-content-between align-items-center {{ $canTakePretest ? 'hover-shadow transition-all bg-white cursor-pointer' : 'bg-light bg-opacity-75' }}" 
                         {{ $canTakePretest ? 'onclick=window.location.href="' . route('siswa.mapels.materis.pretests.show', [$mapel->id, $activeMateri->id]) . '"' : '' }}>
                        
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ ($activeProgress && $activeProgress->is_completed && $activeProgress->pretest_nilai !== null) ? 'bg-success text-white' : 'bg-secondary bg-opacity-25 text-secondary' }}" style="width: 32px; height: 32px;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Kerjakan post test</h6>
                                <p class="text-muted small mb-0">Evaluasi pemahaman modul</p>
                            </div>
                        </div>
                        
                        @if($activeProgress && $activeProgress->is_completed && $activeProgress->pretest_nilai !== null)
                            <div class="text-end me-4">
                                <span class="d-block text-muted" style="font-size: 0.7rem;">NILAI ANDA</span>
                                <span class="fw-bold text-success fs-5">{{ $activeProgress->pretest_nilai }}</span>
                            </div>
                        @else
                            <div class="text-muted">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                        @endif
                        
                    </div>
                    
                    @if(!$canTakePretest)
                    <div class="bg-secondary bg-opacity-10 rounded-bottom-4 px-4 py-3 d-flex align-items-center gap-2 mt-n2 position-relative" style="z-index: -1;">
                        <i class="bi bi-lock-fill text-secondary"></i>
                        <span class="text-secondary small">Pelajari materi di atas terlebih dahulu</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        @else
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                <i class="bi bi-mouse-fill text-muted fs-1 mb-3"></i>
                <h5 class="fw-bold">Pilih Modul Pelatihan</h5>
                <p class="text-muted">Klik salah satu modul di sebelah kiri untuk melihat detail materinya.</p>
            </div>
        @endif
    </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08)!important;
}
.transition-all {
    transition: all 0.3s ease;
}
.hover-underline:hover {
    text-decoration: underline !important;
}
.cursor-pointer {
    cursor: pointer;
}
.accordion-button:not(.collapsed) {
    color: var(--bs-dark);
    background-color: #f8f9fa;
    box-shadow: inset 0 calc(-1 * var(--bs-accordion-border-width)) 0 var(--bs-accordion-border-color);
}
</style>
@endsection
