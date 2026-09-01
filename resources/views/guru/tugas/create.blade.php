@extends('layouts.guru')

@section('title', 'Buat Tugas Baru')
@section('subtitle', 'Mata Pelajaran: ' . $mapel->nama_mapel)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('guru.mapels.tugas.store', $mapel->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Tautkan ke Modul <span class="text-secondary fw-normal">(Opsional)</span></label>
                            <select name="materi_id" class="form-select form-select-lg rounded-3">
                                <option value="">Tugas Umum (Tidak terikat modul tertentu)</option>
                                @foreach($materis as $materi)
                                    <option value="{{ $materi->id }}" {{ old('materi_id') == $materi->id ? 'selected' : '' }}>
                                        Modul {{ $materi->urutan }}: {{ $materi->judul }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih modul jika tugas ini merupakan evaluasi dari modul tersebut.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Judul Tugas <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control form-control-lg rounded-3" placeholder="Contoh: Tugas Praktik 1" value="{{ old('judul') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Instruksi / Deskripsi Tugas <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control rounded-3" rows="5" placeholder="Berikan instruksi yang jelas kepada siswa..." required>{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Batas Waktu (Deadline) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tenggat_waktu" class="form-control form-control-lg rounded-3" value="{{ old('tenggat_waktu') }}" required>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-semibold text-muted">File Lampiran <span class="text-secondary fw-normal">(Opsional)</span></label>
                            <input type="file" name="file_lampiran" class="form-control rounded-3">
                            <div class="form-text">Unggah file soal dalam bentuk PDF/Word jika instruksinya panjang (Maks. 10MB)</div>
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('guru.mapels.tugas.show', $mapel->id) }}" class="btn btn-light rounded-pill px-4 fw-semibold border">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Simpan Tugas</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
