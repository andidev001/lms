@extends('layouts.guru')

@section('title', 'Edit Tugas')
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

                    <form action="{{ route('guru.mapels.tugas.update', [$mapel->id, $tugas->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Tautkan ke Modul <span class="text-secondary fw-normal">(Opsional)</span></label>
                            <select name="materi_id" class="form-select form-select-lg rounded-3">
                                <option value="">Tugas Umum (Tidak terikat modul tertentu)</option>
                                @foreach($materis as $materi)
                                    <option value="{{ $materi->id }}" {{ old('materi_id', $tugas->materi_id) == $materi->id ? 'selected' : '' }}>
                                        Modul {{ $materi->urutan }}: {{ $materi->judul }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Judul Tugas <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control form-control-lg rounded-3" value="{{ old('judul', $tugas->judul) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Instruksi / Deskripsi Tugas <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control rounded-3" rows="5" required>{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Batas Waktu (Deadline) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tenggat_waktu" class="form-control form-control-lg rounded-3" value="{{ old('tenggat_waktu', $tugas->tenggat_waktu->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-semibold text-muted">File Lampiran <span class="text-secondary fw-normal">(Opsional)</span></label>
                            
                            @if($tugas->file_lampiran)
                                <div class="mb-3 p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text text-primary fs-4 me-2"></i>
                                        <div>
                                            <div class="fw-bold small text-dark">File saat ini:</div>
                                            <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank" class="text-decoration-none">Lihat Lampiran</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <input type="file" name="file_lampiran" class="form-control rounded-3">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah file lampiran.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('guru.mapels.tugas.show', $mapel->id) }}" class="btn btn-light rounded-pill px-4 fw-semibold border">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Simpan Perubahan</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
