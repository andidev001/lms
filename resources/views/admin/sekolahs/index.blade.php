@extends('layouts.admin')

@section('title', 'Profil Sekolah')
@section('subtitle', 'Atur informasi dan identitas sistem sekolah')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0">Informasi Sekolah</h5>
            </div>
            <div class="card-body p-4">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (count($errors) > 0)
                    <div class="alert alert-danger rounded-3">
                        <strong>Whoops!</strong> Ada masalah dengan input Anda.<br><br>
                        <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('sekolah.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Nama Sekolah <span class="text-danger">*</span></label>
                        <input type="text" name="nama_sekolah" class="form-control form-control-lg rounded-3" value="{{ old('nama_sekolah', $sekolah->nama_sekolah) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">NPSN</label>
                        <input type="text" name="npsn" class="form-control form-control-lg rounded-3" value="{{ old('npsn', $sekolah->npsn) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Email Sekolah</label>
                            <input type="email" name="email" class="form-control form-control-lg rounded-3" value="{{ old('email', $sekolah->email) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Nomor Telepon</label>
                            <input type="text" name="telepon" class="form-control form-control-lg rounded-3" value="{{ old('telepon', $sekolah->telepon) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Website</label>
                        <input type="url" name="website" class="form-control form-control-lg rounded-3" value="{{ old('website', $sekolah->website) }}" placeholder="https://...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control form-control-lg rounded-3" rows="3">{{ old('alamat', $sekolah->alamat) }}</textarea>
                    </div>

                    <hr class="mb-4">

                    <h6 class="fw-bold mb-3">Kepala Sekolah</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Nama Kepala Sekolah</label>
                            <input type="text" name="kepala_sekolah" class="form-control form-control-lg rounded-3" value="{{ old('kepala_sekolah', $sekolah->kepala_sekolah) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">NIP Kepala Sekolah</label>
                            <input type="text" name="nip_kepala_sekolah" class="form-control form-control-lg rounded-3" value="{{ old('nip_kepala_sekolah', $sekolah->nip_kepala_sekolah) }}">
                        </div>
                    </div>

                    <hr class="mb-4">

                    <h6 class="fw-bold mb-3">Logo Sekolah</h6>
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            <div class="bg-light rounded-4 d-flex align-items-center justify-content-center border" style="width: 100px; height: 100px; overflow: hidden;">
                                @if($sekolah->logo)
                                    <img src="{{ asset('storage/' . $sekolah->logo) }}" alt="Logo Sekolah" style="max-width: 100%; max-height: 100%;">
                                @else
                                    <i class="bi bi-image text-muted fs-1"></i>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <input type="file" name="logo" class="form-control form-control-lg rounded-3 mb-2" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah logo. Format yang didukung: JPG, PNG, GIF (Maks. 2MB)</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white overflow-hidden position-relative">
            <div class="position-absolute end-0 top-0 opacity-10 p-4" style="transform: scale(2) translate(10%, -10%);">
                <i class="bi bi-building fs-1"></i>
            </div>
            <div class="card-body p-4 position-relative z-1">
                <h5 class="fw-bold mb-3">Preview Identitas</h5>
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white rounded p-2 me-3" style="width: 60px; height: 60px; flex-shrink:0;">
                        @if($sekolah->logo)
                            <img src="{{ asset('storage/' . $sekolah->logo) }}" alt="Logo" class="w-100 h-100 object-fit-contain">
                        @else
                            <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-primary fw-bold fs-4 rounded">
                                {{ substr($sekolah->nama_sekolah, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-truncate" style="max-width: 200px;">{{ $sekolah->nama_sekolah }}</h6>
                        <small class="opacity-75">NPSN: {{ $sekolah->npsn ?? '-' }}</small>
                    </div>
                </div>
                
                <div class="mb-2">
                    <i class="bi bi-geo-alt me-2 opacity-75"></i> <small>{{ $sekolah->alamat ?? 'Belum ada alamat' }}</small>
                </div>
                <div class="mb-2">
                    <i class="bi bi-envelope me-2 opacity-75"></i> <small>{{ $sekolah->email ?? 'Belum ada email' }}</small>
                </div>
                <div class="mb-2">
                    <i class="bi bi-telephone me-2 opacity-75"></i> <small>{{ $sekolah->telepon ?? 'Belum ada telepon' }}</small>
                </div>
                
                <hr class="border-white opacity-25 my-3">
                
                <div class="mb-1">
                    <small class="opacity-75 d-block">Kepala Sekolah:</small>
                    <span class="fw-semibold">{{ $sekolah->kepala_sekolah ?? 'Belum diatur' }}</span>
                </div>
                <div>
                    <small class="opacity-75 d-block">NIP:</small>
                    <span class="fw-semibold">{{ $sekolah->nip_kepala_sekolah ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
