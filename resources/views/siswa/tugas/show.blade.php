@extends('layouts.siswa')

@section('title', 'Detail Tugas')
@section('subtitle', $tugas->mapel->nama_mapel)

@section('content')
<div class="row g-4">
    <!-- Kolom Kiri: Detail Tugas -->
    <div class="col-lg-7">
        <div class="mb-4">
            <a href="{{ route('siswa.tugas.index') }}" class="btn btn-light rounded-pill px-4 border shadow-sm fw-semibold">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar Tugas
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center mb-3 text-muted small">
                    <span class="me-3"><i class="bi bi-journal-text me-1"></i> Modul: {{ $tugas->materi ? $tugas->materi->urutan : 'Umum' }}</span>
                    <span><i class="bi bi-calendar-plus me-1"></i> Dibuat: {{ $tugas->created_at->format('d M Y') }}</span>
                </div>
                
                <h4 class="fw-bold text-dark mb-4">{{ $tugas->judul }}</h4>
                
                <div class="p-4 bg-light rounded-4 mb-4 border border-warning border-opacity-50">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-clock-history text-danger fs-4 me-3"></i>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Batas Waktu Pengumpulan</div>
                            <div class="text-danger fw-bold fs-5">{{ $tugas->tenggat_waktu->format('l, d F Y - H:i') }} WIB</div>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Instruksi Tugas:</h6>
                    <div class="text-dark" style="line-height: 1.8;">
                        {!! nl2br(e($tugas->deskripsi)) !!}
                    </div>
                </div>

                @if($tugas->file_lampiran)
                    <div class="p-4 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-white p-3 rounded-3 shadow-sm me-3 text-primary">
                                <i class="bi bi-file-earmark-pdf fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">File Lampiran Tugas</h6>
                                <span class="small text-muted">Unduh file ini untuk detail lebih lanjut</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPreviewSoal">
                            Lihat <i class="bi bi-eye ms-1"></i>
                        </button>
                        

                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Pengumpulan -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-sticky" style="top: 100px;">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-cloud-arrow-up me-2"></i>Pengumpulan Tugas</h5>
            </div>
            <div class="card-body p-4">
                
                @if(session('success'))
                    <div class="alert alert-success rounded-3 mb-4 text-center">
                        <i class="bi bi-check-circle-fill fs-4 d-block mb-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger rounded-3 mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($submission)
                    <div class="mb-4 text-center">
                        <div class="d-inline-flex flex-column align-items-center bg-success bg-opacity-10 text-success rounded-4 p-4 mb-3 w-100 border border-success border-opacity-25">
                            <i class="bi bi-check-circle-fill fs-1 mb-2"></i>
                            <h5 class="fw-bold mb-1">Tugas Terkumpul!</h5>
                            <span class="small opacity-75">{{ $submission->waktu_pengumpulan->format('d M Y, H:i') }}</span>
                        </div>
                        
                        @if($submission->nilai !== null)
                            <div class="p-4 bg-light rounded-4 border mb-4">
                                <span class="d-block text-muted small fw-bold mb-2 text-uppercase">Nilai Anda</span>
                                <div class="display-3 fw-bold text-primary mb-3">{{ $submission->nilai }}</div>
                                
                                @if($submission->catatan_guru)
                                    <div class="text-start bg-white p-3 rounded-3 border">
                                        <div class="fw-bold text-dark small mb-1"><i class="bi bi-chat-quote me-1"></i> Catatan Guru:</div>
                                        <div class="text-muted fst-italic">{{ $submission->catatan_guru }}</div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-info rounded-3 text-start mb-4">
                                <i class="bi bi-info-circle me-2"></i> Jawaban Anda sedang menunggu penilaian dari Guru.
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Form hanya bisa diakses jika belum dinilai -->
                @if(!$submission || $submission->nilai === null)
                    <form action="{{ route('siswa.tugas.submit', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Ketik Jawaban (Opsional)</label>
                            <textarea name="teks_jawaban" class="form-control rounded-3 rich-text" rows="4" placeholder="Ketik jawaban Anda di sini jika tidak menggunakan file...">{{ $submission ? $submission->teks_jawaban : old('teks_jawaban') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Unggah File (Opsional)</label>
                            @if($submission && $submission->file_jawaban)
                                <div class="mb-2 p-2 bg-light rounded border d-flex justify-content-between align-items-center">
                                    <span class="small text-truncate me-2"><i class="bi bi-file-earmark-check text-success me-1"></i> File terkirim</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPreviewJawaban">Lihat</button>
                                </div>
                                

                            @endif
                            <input type="file" name="file_jawaban" class="form-control rounded-3">
                            <div class="form-text small">Upload ulang akan menimpa file sebelumnya.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Link Tambahan (Opsional)</label>
                            <input type="url" name="link_jawaban" class="form-control rounded-3" placeholder="Contoh: Link Google Drive/Youtube" value="{{ $submission ? $submission->link_jawaban : old('link_jawaban') }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm" onclick="return confirm('Apakah Anda yakin jawaban ini sudah final dan ingin dikirimkan?');">
                            <i class="bi bi-send me-2"></i> {{ $submission ? 'Perbarui Jawaban' : 'Kumpulkan Tugas' }}
                        </button>
                    </form>
                @endif
                
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview File Lampiran -->
@if($tugas->file_lampiran)
<div class="modal fade" id="modalPreviewSoal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Lampiran Tugas: {{ $tugas->judul }}</h5>
                <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank" class="btn btn-sm btn-primary ms-3"><i class="bi bi-download"></i> Download</a>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light" style="height: 80vh;">
                <iframe src="{{ Storage::url($tugas->file_lampiran) }}" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Preview File Jawaban -->
@if($submission && $submission->file_jawaban)
<div class="modal fade" id="modalPreviewJawaban" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">File Jawaban Anda</h5>
                <a href="{{ Storage::url($submission->file_jawaban) }}" target="_blank" class="btn btn-sm btn-primary ms-3"><i class="bi bi-download"></i> Download</a>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light" style="height: 80vh;">
                <iframe src="{{ Storage::url($submission->file_jawaban) }}" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    tinymce.init({
        selector: 'textarea.rich-text',
        plugins: 'emoticons link lists',
        toolbar: 'undo redo | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | emoticons link',
        menubar: false,
        height: 250,
        branding: false,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
});
</script>
@endsection
