@extends('layouts.guru')

@section('title', 'Tambah Materi Baru')
@section('subtitle', 'Mata Pelajaran: ' . $mapel->nama_mapel)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Form Input Materi</h5>
                        <a href="{{ route('guru.mapels.materis.index', $mapel->id) }}"
                            class="btn btn-light rounded-pill px-3 shadow-sm border">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                            <strong>Whoops!</strong> Ada masalah dengan input Anda.<br><br>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('guru.mapels.materis.store', $mapel->id) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Judul Materi / Modul <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control form-control-lg rounded-3"
                                placeholder="Contoh: Modul 1: Sistem Persamaan Linear" value="{{ old('judul') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-control rounded-3 rich-text" rows="4"
                                placeholder="Berikan pengantar singkat mengenai apa yang akan dipelajari pada modul ini...">{{ old('deskripsi') }}</textarea>
                        </div>

                        <hr class="my-4 text-muted">

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Upload File (PDF)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white border-danger"><i
                                        class="bi bi-file-earmark-pdf"></i></span>
                                <input type="file" name="file_pdf" class="form-control form-control-lg rounded-end-3"
                                    accept=".pdf">
                            </div>
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Opsional. Maksimal ukuran
                                file: 10MB. Hanya menerima format .pdf</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Link Video (Youtube)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white border-danger"><i
                                        class="bi bi-youtube"></i></span>
                                <input type="url" name="url_youtube" class="form-control form-control-lg rounded-end-3"
                                    placeholder="https://www.youtube.com/watch?v=..." value="{{ old('url_youtube') }}">
                            </div>
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Opsional. Masukkan URL
                                lengkap Youtube.</div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-semibold text-muted">Batas Waktu Modul / Materi Berakhir</label>
                            <input type="datetime-local" name="tenggat_waktu" class="form-control form-control-lg rounded-3"
                                value="{{ old('tenggat_waktu') }}">
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Opsional. Atur batas waktu
                                kapan siswa terakhir kali dapat mengakses atau menyesaikan materi ini.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3 shadow-sm py-3">
                                <i class="bi bi-cloud-arrow-up me-2"></i> Simpan Materi Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
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