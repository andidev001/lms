@php
    $layout = 'layouts.app';
    if (auth()->user()->hasRole('admin')) {
        $layout = 'layouts.admin';
    } elseif (auth()->user()->hasRole('guru')) {
        $layout = 'layouts.guru';
    } elseif (auth()->user()->hasRole('siswa')) {
        $layout = 'layouts.siswa';
    }
@endphp

@extends($layout)

@section('title', 'Profil Saya')
@section('subtitle', 'Kelola informasi profil dan kata sandi Anda')

@section('content')
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">Informasi Profil</h5>
                <p class="text-muted small">Perbarui informasi profil dan alamat email Anda.</p>
            </div>
            <div class="card-body">
                @if (session('success') && !request()->has('password'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <div class="mb-4 text-center">
                        <div class="position-relative d-inline-block mb-3">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover shadow-sm border" style="width: 120px; height: 120px;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 120px; height: 120px; font-size: 3rem;">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <label for="avatar" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-camera me-1"></i> Ganti Foto
                            </label>
                            <input id="avatar" type="file" class="d-none" name="avatar" accept="image/*" onchange="previewImage(this)">
                            @error('avatar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-secondary small">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold text-secondary small">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">Ubah Kata Sandi</h5>
                <p class="text-muted small">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>
            </div>
            <div class="card-body">
                @if (session('success') && session()->has('password'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="post" action="{{ route('profile.password') }}">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold text-secondary small">Kata Sandi Saat Ini</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold text-secondary small">Kata Sandi Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold text-secondary small">Konfirmasi Kata Sandi</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-dark px-4">Perbarui Kata Sandi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                // Try to find existing image
                var img = input.parentElement.parentElement.querySelector('img');
                if (img) {
                    img.src = e.target.result;
                } else {
                    // Create new image and replace the initials div
                    var div = input.parentElement.parentElement.querySelector('.bg-primary.rounded-circle');
                    if (div) {
                        var newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.className = 'rounded-circle object-fit-cover shadow-sm border';
                        newImg.style.width = '120px';
                        newImg.style.height = '120px';
                        div.parentNode.replaceChild(newImg, div);
                    }
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
