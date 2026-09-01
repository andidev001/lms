@extends('layouts.admin')

@section('title', 'Edit Pengumuman')
@section('subtitle', 'Perbarui detail pengumuman')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0">Form Edit Pengumuman</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('pengumumans.update', $pengumuman->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Judul Pengumuman</label>
                        <input type="text" name="judul" class="form-control form-control-lg rounded-3 @error('judul') is-invalid @enderror" value="{{ old('judul', $pengumuman->judul) }}" required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Konten Pengumuman</label>
                        <textarea name="konten" class="form-control rounded-3 @error('konten') is-invalid @enderror" rows="5" required>{{ old('konten', $pengumuman->konten) }}</textarea>
                        @error('konten')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Target Penerima</label>
                            <select name="target" class="form-select form-select-lg rounded-3 @error('target') is-invalid @enderror" required>
                                <option value="semua" {{ old('target', $pengumuman->target) == 'semua' ? 'selected' : '' }}>Semua Pengguna (Guru & Siswa)</option>
                                <option value="guru" {{ old('target', $pengumuman->target) == 'guru' ? 'selected' : '' }}>Hanya Guru</option>
                                <option value="siswa" {{ old('target', $pengumuman->target) == 'siswa' ? 'selected' : '' }}>Hanya Siswa</option>
                            </select>
                            @error('target')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $pengumuman->is_active) ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                                <label class="form-check-label ms-2 mt-1 fw-semibold text-muted" for="is_active">Aktif & Tampilkan</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-5">
                        <a href="{{ route('pengumumans.index') }}" class="btn btn-light rounded-pill px-4 border">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
