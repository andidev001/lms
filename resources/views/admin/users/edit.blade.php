@extends('layouts.admin')

@section('title', 'Edit Data User')
@section('subtitle', 'Perbarui informasi pengguna yang sudah ada')

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0">Informasi Pengguna</h5>
            </div>
            <div class="card-body p-4">
                @if (count($errors) > 0)
                    <div class="alert alert-danger rounded-3">
                        <strong>Whoops!</strong> Ada masalah dengan input Anda.<br><br>
                        <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-muted">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control form-control-lg rounded-3" id="name" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-muted">Alamat Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-control form-control-lg rounded-3" id="email" placeholder="contoh: budi@sekolah.com" required>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark small rounded-3">
                                <i class="bi bi-info-circle me-1"></i> Biarkan field password kosong jika Anda tidak ingin mengubahnya.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold text-muted">Password Baru</label>
                            <input type="password" name="password" class="form-control form-control-lg rounded-3" id="password" placeholder="Minimal 8 karakter">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm-password" class="form-label fw-semibold text-muted">Konfirmasi Password</label>
                            <input type="password" name="confirm-password" class="form-control form-control-lg rounded-3" id="confirm-password" placeholder="Ulangi password">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="roles" class="form-label fw-semibold text-muted">Jabatan (Role)</label>
                        <select name="roles[]" id="roles" class="form-select form-select-lg rounded-3" required>
                            <option value="">-- Pilih Role Pengguna --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ in_array($role, $userRole) ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="text-muted opacity-25 my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5">Perbarui Data</button>
                        <a href="{{ route('users.index') }}" class="btn btn-light btn-lg rounded-3 px-4 border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
