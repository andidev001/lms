@extends('layouts.guru')

@section('title', 'Kelola Soal Post-Test')
@section('subtitle', 'Materi: ' . $materi->judul)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="{{ route('guru.mapels.materis.index', $mapel->id) }}" class="btn btn-light rounded-pill px-3 border shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Kembali ke List Materi
    </a>
    <div class="d-flex gap-2 flex-wrap">
        @if($materi->pretest_questions->count() > 0)
        <button type="button" class="btn btn-info text-white rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#previewSoalModal">
            <i class="bi bi-laptop me-2"></i> Preview Soal (Simulasi Siswa)
        </button>
        @endif
        <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#importWordModal">
            <i class="bi bi-file-earmark-word me-2"></i> Upload Soal (Word / TXT)
        </button>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            <i class="bi bi-plus-circle me-2"></i> Tambah Soal Manual
        </button>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Gagal:</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Terdapat kesalahan input:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">Modul {{ $materi->urutan }}: {{ $materi->judul }}</h5>
                <p class="text-muted mb-0 small">{{ Str::limit(strip_tags($materi->deskripsi), 120) }}</p>
            </div>
            <div class="text-end">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2">
                    <i class="bi bi-ui-checks-grid me-1"></i> Total Soal: {{ $materi->pretest_questions->count() }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Tampilan Daftar Soal Menggunakan DataTables yang Ringkas dan Efisien -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-table me-2 text-primary"></i>Daftar Soal & Kunci Jawaban</h6>
        <div class="d-flex gap-2 align-items-center">
            @if($materi->pretest_questions->count() > 0)
            <button type="button" class="btn btn-sm btn-outline-info text-info-emphasis fw-semibold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#previewSoalModal">
                <i class="bi bi-display me-1"></i> Preview Mode Siswa
            </button>
            @endif
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">Mode Tabel Efisien</span>
        </div>
    </div>
    <div class="card-body p-4">
        @if($materi->pretest_questions->count() > 0)
            <div class="table-responsive">
                <table id="questionsTable" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th width="5%" class="text-center py-3">No</th>
                            <th width="35%" class="py-3">Pertanyaan</th>
                            <th width="35%" class="py-3">Pilihan Opsi & Jawaban</th>
                            <th width="10%" class="text-center py-3">Kunci</th>
                            <th width="15%" class="text-center py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materi->pretest_questions as $index => $question)
                        <tr>
                            <td class="text-center fw-bold text-secondary">{{ $index + 1 }}</td>
                            <td class="pe-3">
                                <div class="text-dark py-1" style="max-height: 140px; overflow-y: auto; font-size: 0.95rem;">
                                    {!! $question->pertanyaan !!}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1 py-1">
                                    @foreach(['A' => $question->opsi_a, 'B' => $question->opsi_b, 'C' => $question->opsi_c, 'D' => $question->opsi_d, 'E' => $question->opsi_e] as $letter => $opsi)
                                        @if($opsi)
                                            <div class="px-2 py-1 rounded border {{ $question->jawaban_benar == $letter ? 'bg-success bg-opacity-10 border-success text-dark fw-bold' : 'bg-light text-muted' }}" style="font-size: 0.84rem;">
                                                <span class="badge {{ $question->jawaban_benar == $letter ? 'bg-success' : 'bg-secondary' }} me-2" style="width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">{{ $letter }}</span>
                                                {{ $opsi }}
                                                @if($question->jawaban_benar == $letter)
                                                    <i class="bi bi-check-circle-fill text-success ms-1" title="Kunci Jawaban"></i>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm" style="font-size: 0.88rem;">
                                    Opsi {{ $question->jawaban_benar }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $question->id }}">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </button>
                                    <form action="{{ route('guru.mapels.materis.pretests.destroy', [$mapel->id, $materi->id, $question->id]) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2" onclick="return confirm('Yakin menghapus soal ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                    <i class="bi bi-question-square text-muted fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark">Belum Ada Soal Post-Test</h5>
                <p class="text-muted mb-4">Materi ini belum memiliki soal Post-Test. Silakan upload file Word atau tambahkan manual.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#importWordModal">
                        <i class="bi bi-file-earmark-word me-2"></i> Upload Soal (Word / TXT)
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="bi bi-plus-circle me-2"></i> Tambah Soal Manual
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Preview Soal Modal (Simulasi Siswa) -->
<div class="modal fade" id="previewSoalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header -->
            <div class="modal-header bg-dark text-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-25 text-primary p-2 rounded-3">
                        <i class="bi bi-laptop fs-3 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white">Preview Simulasi Post-Test</h5>
                        <p class="text-light text-opacity-75 small mb-0">Tampilan persis seperti yang akan dikerjakan oleh siswa pada layar mereka.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check form-switch bg-light bg-opacity-10 px-4 py-2 rounded-pill d-flex align-items-center gap-3 border border-light border-opacity-25 m-0">
                        <input class="form-check-input m-0" type="checkbox" role="switch" id="toggleKunciJawaban" style="cursor: pointer; width: 2.5em; height: 1.3em;">
                        <label class="form-check-label small fw-bold text-warning text-nowrap" for="toggleKunciJawaban" style="cursor: pointer; letter-spacing: 0.3px;">
                            <i class="bi bi-key-fill me-1"></i> Intip Kunci Jawaban
                        </label>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Body -->
            <div class="modal-body p-4 bg-light">
                <!-- Info Banner -->
                <div class="alert alert-light border shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3 p-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                        <span class="fw-semibold text-dark">Modul: {{ $materi->judul }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-white text-dark border px-3 py-2 rounded-pill"><i class="bi bi-ui-checks me-1 text-primary"></i> {{ $materi->pretest_questions->count() }} Butir Soal</span>
                        <span class="badge bg-white text-dark border px-3 py-2 rounded-pill"><i class="bi bi-clock me-1 text-danger"></i> Simulasi Waktu</span>
                    </div>
                </div>

                <!-- Lembar Soal -->
                <div class="row g-4">
                    <div class="col-lg-12">
                        @forelse($materi->pretest_questions as $index => $question)
                            <div class="card border border-light-subtle shadow-sm rounded-4 mb-4 question-preview-card" id="preview-q-{{ $index + 1 }}">
                                <div class="card-header bg-white border-bottom pt-3 pb-3 px-4 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 fw-bold">
                                            Soal Ke-{{ $index + 1 }}
                                        </span>
                                        <span class="text-muted small">Pilihan Ganda</span>
                                    </div>
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-2 rounded-pill d-none kunci-jawaban-tag shadow-sm">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i> Kunci Benar: <strong class="text-success fs-6 ms-1">Opsi {{ $question->jawaban_benar }}</strong>
                                    </span>
                                </div>
                                <div class="card-body p-4">
                                    <!-- Teks Pertanyaan -->
                                    <div class="fs-6 fw-semibold text-dark mb-4 pb-3 border-bottom" style="line-height: 1.6;">
                                        {!! $question->pertanyaan !!}
                                    </div>

                                    <!-- Opsi Pilihan (A - E) -->
                                    <div class="d-flex flex-column gap-3">
                                        @foreach(['A' => $question->opsi_a, 'B' => $question->opsi_b, 'C' => $question->opsi_c, 'D' => $question->opsi_d, 'E' => $question->opsi_e] as $letter => $opsi)
                                            @if($opsi)
                                                <label class="p-3 rounded-4 border border-2 bg-white d-flex align-items-center gap-3 opsi-item text-dark shadow-sm" style="cursor: pointer; transition: all 0.2s;" data-letter="{{ $letter }}" data-key="{{ $question->jawaban_benar == $letter ? 'true' : 'false' }}">
                                                    <div class="form-check m-0">
                                                        <input class="form-check-input fs-5 m-0" type="radio" name="preview_q_{{ $question->id }}" id="preview_{{ $question->id }}_{{ $letter }}" value="{{ $letter }}" style="cursor: pointer;">
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                                                        <span class="badge bg-light text-secondary border fw-bold px-2 py-1 fs-6" style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">{{ $letter }}</span>
                                                        <span class="fs-6">{{ $opsi }}</span>
                                                    </div>
                                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill d-none kunci-indikator shadow-sm">
                                                        <i class="bi bi-check-lg me-1"></i> Kunci Jawaban
                                                    </span>
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <p class="text-muted">Belum ada soal untuk dipreview.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                    <span class="text-muted small"><i class="bi bi-shield-exclamation me-1 text-primary"></i>Ini adalah fitur simulasi interaktif. Jawaban yang dipilih dalam preview ini tidak mempegaruh penilaian siswa.</span>
                    <button type="button" class="btn btn-secondary rounded-pill px-5 fw-bold shadow-sm" data-bs-dismiss="modal">Tutup Preview</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Word / TXT Modal -->
<div class="modal fade" id="importWordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom pt-4 pb-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-file-earmark-word text-success me-2"></i>Upload Soal via Word / TXT</h5>
                    <p class="text-muted small mb-0">Impor puluhan atau ratusan soal sekaligus menggunakan format berkas dokumen standar.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('guru.mapels.materis.pretests.import-word', [$mapel->id, $materi->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Bagian Download Template -->
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-dark rounded-4 p-4 mb-4 shadow-sm">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-download me-2"></i>Belum punya format file Word-nya?</h6>
                                <p class="small text-muted mb-0">Unduh template format resmi kami yang langsung bisa dibuka dan diedit di Microsoft Word.</p>
                            </div>
                            <a href="{{ route('guru.mapels.materis.pretests.download-template', [$mapel->id, $materi->id]) }}" class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-semibold shadow-sm text-nowrap">
                                <i class="bi bi-file-earmark-word-fill me-1"></i> Download Format Word
                            </a>
                        </div>
                    </div>

                    <!-- Panduan Singkat Format -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Contoh Penulisan Soal di dalam Dokumen:</label>
                        <div class="p-3 bg-dark text-light rounded-3 font-monospace small shadow-inner" style="line-height: 1.6; letter-spacing: 0.3px; border: 1px solid #334155;">
                            <span class="text-warning">S:1)</span> Jaringan komputer yang mencakup area sangat luas disebut ....<br>
                            <span class="text-info">A:)</span> LAN<br>
                            <span class="text-info">B:)</span> MAN<br>
                            <span class="text-info">C:)</span> WAN<br>
                            <span class="text-info">D:)</span> PAN<br>
                            <span class="text-info">E:)</span> WLAN<br>
                            <span class="text-success fw-bold">JAWABAN: C</span><br>
                            <br>
                            <span class="text-warning">S:2)</span> Perangkat lunak yang berfungsi sebagai sistem operasi komputer adalah ....<br>
                            <span class="text-info">A:)</span> Microsoft Word<br>
                            <span class="text-info">B:)</span> Linux Ubuntu<br>
                            <span class="text-info">C:)</span> Google Chrome<br>
                            <span class="text-info">D:)</span> Adobe Photoshop<br>
                            <span class="text-info">E:)</span> Corel Draw<br>
                            <span class="text-success fw-bold">JAWABAN: B</span>
                        </div>
                    </div>

                    <!-- Input Upload File -->
                    <div class="mb-3">
                        <label for="file_word" class="form-label fw-bold text-dark">Pilih File untuk Diunggah <span class="text-danger">*</span></label>
                        <input class="form-control form-control-lg rounded-3 border-2 @error('file_word') is-invalid @enderror" type="file" id="file_word" name="file_word" accept=".docx,.doc,.txt" required>
                        <div class="form-text mt-2"><i class="bi bi-shield-check text-success me-1"></i> Format file yang didukung: <strong>.docx, .doc, dan .txt</strong> (Maksimal 5 MB)</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                        <i class="bi bi-cloud-upload me-2"></i> Proses Upload Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modals -->
@foreach ($materi->pretest_questions as $question)
<div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="modal-title fw-bold">Edit Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('guru.mapels.materis.pretests.update', [$mapel->id, $materi->id, $question->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="pertanyaan" class="form-control rounded-3 rich-text" rows="3" required>{{ $question->pertanyaan }}</textarea>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        @foreach(['A' => $question->opsi_a, 'B' => $question->opsi_b, 'C' => $question->opsi_c, 'D' => $question->opsi_d, 'E' => $question->opsi_e] as $key => $val)
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Opsi {{ $key }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark fw-bold border-end-0">{{ $key }}</span>
                                <input type="text" name="opsi_{{ strtolower($key) }}" class="form-control border-start-0" value="{{ $val }}" required>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted">Kunci Jawaban Benar <span class="text-danger">*</span></label>
                        <select name="jawaban_benar" class="form-select form-select-lg rounded-3" required>
                            @foreach(['A','B','C','D','E'] as $key)
                                <option value="{{ $key }}" {{ $question->jawaban_benar == $key ? 'selected' : '' }}>Opsi {{ $key }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-5">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="modal-title fw-bold">Tambah Soal Post-Test Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('guru.mapels.materis.pretests.store', [$mapel->id, $materi->id]) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="pertanyaan" class="form-control rounded-3 rich-text" rows="3" placeholder="Ketik pertanyaan Anda di sini..." required></textarea>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        @foreach(['A','B','C','D','E'] as $key)
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Opsi {{ $key }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark fw-bold border-end-0">{{ $key }}</span>
                                <input type="text" name="opsi_{{ strtolower($key) }}" class="form-control border-start-0" placeholder="Pilihan {{ $key }}" required>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted">Kunci Jawaban Benar <span class="text-danger">*</span></label>
                        <select name="jawaban_benar" class="form-select form-select-lg rounded-3" required>
                            <option value="">-- Pilih Jawaban Benar --</option>
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                            <option value="E">Opsi E</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-5">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix Bootstrap 5 modal focus issue dengan TinyMCE
        document.addEventListener('focusin', (e) => {
            if (e.target.closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
                e.stopImmediatePropagation();
            }
        });

        function initTinyMCEForModals() {
            var modals = document.querySelectorAll('.modal');
            
            modals.forEach(function(modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    var textarea = modal.querySelector('textarea.rich-text');
                    if (textarea) {
                        if (!textarea.id) {
                            textarea.id = 'editor-' + Math.random().toString(36).substr(2, 9);
                        }
                        tinymce.init({
                            selector: '#' + textarea.id,
                            plugins: 'lists link table code',
                            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | table link | code',
                            menubar: false,
                            height: 250,
                            branding: false,
                            setup: function (editor) {
                                editor.on('change', function () {
                                    editor.save();
                                });
                            }
                        });
                    }
                });

                modal.addEventListener('hidden.bs.modal', function() {
                    var textarea = modal.querySelector('textarea.rich-text');
                    if (textarea && textarea.id) {
                        var editor = tinymce.get(textarea.id);
                        if (editor) {
                            editor.remove();
                        }
                    }
                });
            });
        }

        initTinyMCEForModals();

        // Fitur Toggle Intip Kunci Jawaban di Modal Preview Soal
        const toggleKunci = document.getElementById('toggleKunciJawaban');
        if (toggleKunci) {
            toggleKunci.addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.kunci-jawaban-tag').forEach(tag => {
                    tag.classList.toggle('d-none', !isChecked);
                });
                document.querySelectorAll('.opsi-item[data-key="true"]').forEach(item => {
                    if (isChecked) {
                        item.classList.remove('bg-white', 'border-light-subtle');
                        item.classList.add('bg-success', 'bg-opacity-10', 'border-success', 'fw-bold');
                        item.style.borderColor = '#198754';
                        item.querySelector('.kunci-indikator')?.classList.remove('d-none');
                    } else {
                        item.classList.add('bg-white');
                        item.classList.remove('bg-success', 'bg-opacity-10', 'border-success', 'fw-bold');
                        const radio = item.querySelector('input[type="radio"]');
                        if (radio && radio.checked) {
                            item.style.borderColor = '#0d6efd';
                        } else {
                            item.style.borderColor = '#dee2e6';
                        }
                        item.querySelector('.kunci-indikator')?.classList.add('d-none');
                    }
                });
            });
        }

        // Efek interaktif saat memilih radio button pada Preview Soal
        document.querySelectorAll('.opsi-item input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const card = this.closest('.question-preview-card');
                card.querySelectorAll('.opsi-item').forEach(el => {
                    if (toggleKunci && toggleKunci.checked && el.getAttribute('data-key') === 'true') {
                        el.style.borderColor = '#198754';
                    } else {
                        el.style.borderColor = '#dee2e6';
                    }
                    el.style.boxShadow = 'none';
                });
                const selectedOpsi = this.closest('.opsi-item');
                selectedOpsi.style.borderColor = '#0d6efd';
                selectedOpsi.style.boxShadow = '0 0 0 3px rgba(13, 110, 253, 0.15)';
            });
        });
    });

    $(document).ready(function() {
        if ($('#questionsTable').length) {
            $('#questionsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                ordering: false,
                lengthMenu: [5, 10, 25, 50]
            });
        }
    });
</script>
@endpush
@endsection
