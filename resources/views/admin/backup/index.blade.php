@extends('layouts.admin')

@section('title', 'Backup & Restore Database')
@section('subtitle', 'Kelola cadangan dan pemulihan data sistem pembelajaran')

@section('content')
<div class="row g-4">
    <!-- Bagian Backup Database -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-database-down fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Backup Database</h5>
                            <p class="text-muted small mb-0">Unduh salinan cadangan data sistem</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">
                        Fitur ini memungkinkan Anda untuk mengunduh seluruh struktur dan isi data aplikasi (seperti data Siswa, Guru, Kelas, Materi, Tugas, dan Nilai) dalam satu file berekstensi <code>.sql</code>.
                    </p>

                    <div class="bg-light rounded-3 p-3 mb-4 border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small"><i class="bi bi-hdd me-1"></i> Nama Database:</span>
                            <span class="fw-semibold small">{{ $dbName }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small"><i class="bi bi-table me-1"></i> Jumlah Tabel:</span>
                            <span class="fw-semibold small">{{ $totalTables }} Tabel</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small"><i class="bi bi-cpu me-1"></i> Database Driver:</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">{{ strtoupper($driver) }}</span>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-dark mb-0 rounded-3 text-start small">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        <strong>Anjuran:</strong> Lakukan backup secara berkala (misal seminggu sekali) untuk menjaga keamanan data dari kerusakan atau kehilangan.
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 px-4 pb-4 pt-0 text-end">
                <form action="{{ route('backup.download') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-3 shadow-sm fw-bold w-100">
                        <i class="bi bi-cloud-arrow-down me-2"></i> Download Backup Database (.SQL)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bagian Restore Database -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-database-up fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Restore Database</h5>
                            <p class="text-muted small mb-0">Pulihkan data sistem dari file backup</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">
                        Unggah file cadangan (berformat <code>.sql</code>) untuk mengembalikan kondisi sistem dan database persis seperti pada saat file cadangan tersebut dibuat.
                    </p>

                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark mb-4 rounded-3 text-start small">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                            <div>
                                <strong>PERINGATAN PENTING!</strong><br>
                                Proses Restore akan <strong>menghapus dan menimpa (replace)</strong> seluruh data yang ada di database saat ini dengan isi data yang terdapat dalam file <code>.sql</code> yang Anda pilih. Pastikan Anda sudah siap dengan konsekuensinya.
                            </div>
                        </div>
                    </div>

                    <form id="form-restore" action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Pilih File Backup (.sql) <span class="text-danger">*</span></label>
                            <input type="file" name="backup_file" class="form-control form-control-lg rounded-3 @error('backup_file') is-invalid @enderror" accept=".sql" required>
                            @error('backup_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-warning rounded-pill px-4 py-3 shadow-sm fw-bold w-100 text-dark" onclick="return confirm('PERINGATAN KRUSIAL! Seluruh data saat ini akan DITIMPA total oleh isi file backup. Apakah Anda yakin ingin melanjutkan proses restore database ini?')">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Pulihkan Database (Restore) Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
