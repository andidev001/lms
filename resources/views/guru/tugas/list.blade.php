@extends('layouts.guru')

@section('title', 'Kelola Tugas')
@section('subtitle', 'Mata Pelajaran: ' . $mapel->nama_mapel)

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('guru.tugas.index') }}" class="btn btn-light rounded-pill px-4 border shadow-sm fw-semibold">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalBuatTugas">
            <i class="bi bi-plus-lg me-2"></i> Buat Tugas Baru
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase text-secondary fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">No</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Judul Tugas</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Modul / Materi</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Tenggat Waktu</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Pengumpulan</th>
                            <th class="text-center pe-4 py-3 text-uppercase text-secondary fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tugases as $index => $tugas)
                            <tr>
                                <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $tugas->judul }}</div>
                                    @if($tugas->file_lampiran)
                                        <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank" class="badge bg-info text-dark text-decoration-none mt-1">
                                            <i class="bi bi-paperclip"></i> Ada Lampiran
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($tugas->materi)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">Modul {{ $tugas->materi->urutan }}</span>
                                    @else
                                        <span class="text-muted small">Umum</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar-event me-2 text-warning"></i>
                                        <span>{{ $tugas->tenggat_waktu->format('d M Y, H:i') }}</span>
                                    </div>
                                    @if($tugas->tenggat_waktu < now())
                                        <span class="badge bg-danger mt-1">Berakhir</span>
                                    @else
                                        <span class="badge bg-success mt-1">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">
                                        {{ $tugas->submissions()->count() }} Siswa
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('guru.mapels.tugas.penilaian.index', [$mapel->id, $tugas->id]) }}" class="btn btn-sm btn-info text-white rounded-circle shadow-sm" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;" title="Periksa Jawaban">
                                            <i class="bi bi-check2-square"></i>
                                        </a>
                                        <a href="{{ route('guru.mapels.tugas.edit', [$mapel->id, $tugas->id]) }}" class="btn btn-sm btn-warning text-white rounded-circle shadow-sm" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;" title="Edit Tugas">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('guru.mapels.tugas.destroy', [$mapel->id, $tugas->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus tugas ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-circle shadow-sm" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;" title="Hapus Tugas">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/amber/student-going-to-school.svg" alt="Empty" style="height: 150px;" class="mb-3 opacity-50">
                                    <h6 class="fw-bold text-muted">Belum ada tugas</h6>
                                    <p class="text-muted small mb-0">Klik "Buat Tugas Baru" untuk mulai memberikan penugasan ke siswa.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('modalBuatTugas'));
                modal.show();
            });
        </script>
    @endif

    <!-- Modal Buat Tugas -->
    <div class="modal fade" id="modalBuatTugas" tabindex="-1" aria-labelledby="modalBuatTugasLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalBuatTugasLabel">Buat Tugas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('guru.mapels.tugas.store', $mapel->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body py-4">
                        @if($errors->any())
                            <div class="alert alert-danger rounded-3 mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Tautkan ke Modul <span class="text-secondary fw-normal">(Opsional)</span></label>
                            <select name="materi_id" class="form-select rounded-3">
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
                            <input type="text" name="judul" class="form-control rounded-3" placeholder="Contoh: Tugas Praktik 1" value="{{ old('judul') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Instruksi / Deskripsi Tugas <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control rounded-3" rows="4" placeholder="Berikan instruksi yang jelas kepada siswa..." required>{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Batas Waktu (Deadline) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tenggat_waktu" class="form-control rounded-3" value="{{ old('tenggat_waktu') }}" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">File Lampiran <span class="text-secondary fw-normal">(Opsional)</span></label>
                            <input type="file" name="file_lampiran" class="form-control rounded-3">
                            <div class="form-text">Unggah file soal dalam bentuk PDF/Word jika instruksinya panjang (Maks. 10MB)</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan Tugas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
