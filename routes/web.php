<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'password'])->name('profile.password');

    Route::resource('categories', App\Http\Controllers\CategoryController::class);
    Route::resource('courses', App\Http\Controllers\CourseController::class);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('users-bulk-destroy', [App\Http\Controllers\Admin\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::post('users/{id}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('users-export-guru', [App\Http\Controllers\Admin\UserController::class, 'exportGuru'])->name('users.export-guru');
    Route::get('users-export-siswa', [App\Http\Controllers\Admin\UserController::class, 'exportSiswa'])->name('users.export-siswa');
    
    // Guru Routes
    Route::resource('gurus', App\Http\Controllers\Admin\GuruController::class);
    Route::post('gurus-bulk-destroy', [App\Http\Controllers\Admin\GuruController::class, 'bulkDestroy'])->name('gurus.bulk-destroy');
    Route::post('gurus/{id}/reset-password', [App\Http\Controllers\Admin\GuruController::class, 'resetPassword'])->name('gurus.reset-password');
    Route::post('gurus/{id}/generate-account', [App\Http\Controllers\Admin\GuruController::class, 'generateAccount'])->name('gurus.generate-account');
    Route::post('gurus-generate-all', [App\Http\Controllers\Admin\GuruController::class, 'generateAllAccount'])->name('gurus.generate-all');
    Route::get('gurus-template', [App\Http\Controllers\Admin\GuruController::class, 'downloadTemplate'])->name('gurus.template');
    Route::post('gurus-import', [App\Http\Controllers\Admin\GuruController::class, 'importExcel'])->name('gurus.import');

    // Siswa Routes
    Route::resource('siswas', App\Http\Controllers\Admin\SiswaController::class);
    Route::post('siswas-bulk-destroy', [App\Http\Controllers\Admin\SiswaController::class, 'bulkDestroy'])->name('siswas.bulk-destroy');
    Route::post('siswas/{id}/reset-password', [App\Http\Controllers\Admin\SiswaController::class, 'resetPassword'])->name('siswas.reset-password');
    Route::post('siswas/{id}/generate-account', [App\Http\Controllers\Admin\SiswaController::class, 'generateAccount'])->name('siswas.generate-account');
    Route::post('siswas-generate-all', [App\Http\Controllers\Admin\SiswaController::class, 'generateAllAccount'])->name('siswas.generate-all');
    Route::get('siswas-template', [App\Http\Controllers\Admin\SiswaController::class, 'downloadTemplate'])->name('siswas.template');
    Route::post('siswas-import', [App\Http\Controllers\Admin\SiswaController::class, 'importExcel'])->name('siswas.import');

    // Kelas Routes
    Route::resource('kelas', App\Http\Controllers\Admin\KelasController::class);

    // Mapel Routes
    Route::resource('mapels', App\Http\Controllers\Admin\MapelController::class);

    // Tahun Ajaran Routes
    Route::post('tahun-ajarans/{id}/set-aktif', [App\Http\Controllers\Admin\TahunAjaranController::class, 'setAktif'])->name('tahun-ajarans.set-aktif');
    Route::resource('tahun-ajarans', App\Http\Controllers\Admin\TahunAjaranController::class);

    // Pengaturan Sekolah Routes
    Route::get('pengaturan/sekolah', [App\Http\Controllers\Admin\SekolahController::class, 'index'])->name('sekolah.index');
    Route::put('pengaturan/sekolah', [App\Http\Controllers\Admin\SekolahController::class, 'update'])->name('sekolah.update');
    Route::get('pengaturan/backup-restore', [App\Http\Controllers\Admin\BackupRestoreController::class, 'index'])->name('backup.index');
    Route::post('pengaturan/backup-restore/backup', [App\Http\Controllers\Admin\BackupRestoreController::class, 'backup'])->name('backup.download');
    Route::post('pengaturan/backup-restore/restore', [App\Http\Controllers\Admin\BackupRestoreController::class, 'restore'])->name('backup.restore');

    // Pengumuman Routes
    Route::resource('pengumumans', App\Http\Controllers\Admin\PengumumanController::class);

    // Laporan Routes
    Route::get('laporans/akademik', [App\Http\Controllers\Admin\LaporanController::class, 'akademik'])->name('laporans.akademik');
    Route::get('laporans/akademik/export', [App\Http\Controllers\Admin\LaporanController::class, 'exportAkademik'])->name('laporans.akademik.export');
});

// Guru Routes
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('guru.dashboard');
    });
    Route::get('/dashboard', [App\Http\Controllers\Guru\DashboardController::class, 'index'])->name('dashboard');
    
    // Mapel -> Materi
    Route::prefix('mapels/{mapel}')->name('mapels.')->group(function() {
        Route::resource('materis', App\Http\Controllers\Guru\MateriController::class);
        Route::get('materis/{materi}/pretests/download-template', [App\Http\Controllers\Guru\PretestController::class, 'downloadTemplate'])->name('materis.pretests.download-template');
        Route::get('materis/{materi}/pretests/import-word', function($mapel, $materi) {
            return redirect()->route('guru.mapels.materis.show', [$mapel, $materi])->with('error', 'Silakan pilih file soal untuk diupload melalui tombol Upload Soal.');
        });
        Route::post('materis/{materi}/pretests/import-word', [App\Http\Controllers\Guru\PretestController::class, 'importWord'])->name('materis.pretests.import-word');
        Route::resource('materis.pretests', App\Http\Controllers\Guru\PretestController::class)->except(['index', 'create', 'show', 'edit']);
        
        // List tugas untuk mapel
        Route::get('tugas', [App\Http\Controllers\Guru\TugasController::class, 'show'])->name('tugas.show');
        Route::resource('tugas', App\Http\Controllers\Guru\TugasController::class)->except(['index', 'show']);
        
        // Penilaian Tugas
        Route::get('tugas/{tugas}/penilaian', [App\Http\Controllers\Guru\TugasPenilaianController::class, 'index'])->name('tugas.penilaian.index');
        Route::get('tugas/{tugas}/penilaian/data', [App\Http\Controllers\Guru\TugasPenilaianController::class, 'data'])->name('tugas.penilaian.data');
        Route::get('tugas/{tugas}/penilaian/export', [App\Http\Controllers\Guru\TugasPenilaianController::class, 'export'])->name('tugas.penilaian.export');
        Route::post('tugas/{tugas}/penilaian/{submission}', [App\Http\Controllers\Guru\TugasPenilaianController::class, 'store'])->name('tugas.penilaian.store');
    });

    // Kelola Tugas (Index)
    Route::get('/tugas', [App\Http\Controllers\Guru\TugasController::class, 'index'])->name('tugas.index');
    
    // Progress / Rekap Nilai
    Route::get('/progress', [App\Http\Controllers\Guru\ProgressController::class, 'index'])->name('progress.index');
    Route::get('/progress/mapel/{mapel}/kelas/{kelas}', [App\Http\Controllers\Guru\ProgressController::class, 'show'])->name('progress.show');
    Route::get('/progress/data/mapel/{mapel}/kelas/{kelas}', [App\Http\Controllers\Guru\ProgressController::class, 'data'])->name('progress.data');
    Route::get('/progress/export/mapel/{mapel}/kelas/{kelas}', [App\Http\Controllers\Guru\ProgressController::class, 'export'])->name('progress.export');
});

// Siswa Routes
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('siswa.dashboard');
    });
    Route::get('/dashboard', [App\Http\Controllers\SiswaController::class, 'index'])->name('dashboard');

    // Modul Belajar
    Route::get('/belajar', [App\Http\Controllers\Siswa\MateriSiswaController::class, 'index'])->name('belajar');
    Route::prefix('belajar/mapels/{mapel}')->name('mapels.')->group(function() {
        Route::get('/', [App\Http\Controllers\Siswa\MateriSiswaController::class, 'showMapel'])->name('show');
        Route::get('/materis/{materi}', [App\Http\Controllers\Siswa\MateriSiswaController::class, 'showMateri'])->name('materis.show');
        Route::post('/materis/{materi}/mark', [App\Http\Controllers\Siswa\MateriSiswaController::class, 'markProgress'])->name('materis.mark');
        Route::get('/materis/{materi}/pretest', [App\Http\Controllers\Siswa\MateriSiswaController::class, 'showPretest'])->name('materis.pretests.show');
        Route::post('/materis/{materi}/pretest', [App\Http\Controllers\Siswa\MateriSiswaController::class, 'submitPretest'])->name('materis.pretests.submit');
    });

    // Tugas Siswa
    Route::get('tugas', [App\Http\Controllers\Siswa\TugasSiswaController::class, 'index'])->name('tugas.index');
    Route::get('tugas/{id}', [App\Http\Controllers\Siswa\TugasSiswaController::class, 'show'])->name('tugas.show');
    Route::post('tugas/{id}/submit', [App\Http\Controllers\Siswa\TugasSiswaController::class, 'submit'])->name('tugas.submit');

    // Nilai Siswa
    Route::get('nilai', [App\Http\Controllers\Siswa\NilaiController::class, 'index'])->name('nilai.index');
});
