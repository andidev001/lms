@extends('layouts.guru')

@section('title', 'Penilaian Tugas')
@section('subtitle', 'Tugas: ' . $tugas->judul)

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('guru.mapels.tugas.show', $tugas->mapel_id) }}" class="btn btn-light rounded-pill px-4 border shadow-sm fw-semibold">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar Tugas
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 bg-primary bg-opacity-10">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="fw-bold text-primary mb-2">{{ $tugas->judul }}</h5>
                            <p class="text-dark mb-2">{{ $tugas->deskripsi }}</p>
                            <div class="d-flex align-items-center text-muted small mt-3">
                                <span class="me-4"><i class="bi bi-clock me-1 text-warning"></i> Tenggat: {{ $tugas->tenggat_waktu->format('d M Y, H:i') }}</span>
                                <span><i class="bi bi-book me-1 text-secondary"></i> Modul: {{ $tugas->materi ? $tugas->materi->urutan : 'Umum' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            @if($tugas->file_lampiran)
                                <button type="button" class="btn btn-outline-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPreviewSoal">
                                    <i class="bi bi-eye me-1"></i> Lihat Lampiran Soal
                                </button>
                                
                                <!-- Modal Preview File Lampiran -->
                                <div class="modal fade" id="modalPreviewSoal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered text-start">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-bottom-0">
                                                <h5 class="modal-title fw-bold">Lampiran Tugas: {{ $tugas->judul }}</h5>
                                                <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank" class="btn btn-sm btn-primary ms-3"><i class="bi bi-download"></i> Download</a>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0 bg-light" style="height: 80vh;">
                                                <iframe src="{{ Storage::url($tugas->file_lampiran) }}" style="width: 100%; height: 100%; border: none;"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2"></i>Daftar Pengumpulan</h5>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('guru.mapels.tugas.penilaian.export', [$tugas->mapel_id, $tugas->id]) }}?kelas_id={{ $kelas_id }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm d-flex align-items-center" id="btnExportExcel">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel
                </a>
                <span class="text-muted small fw-semibold text-nowrap ms-2">Filter Kelas:</span>
                <select id="filterKelas" class="form-select form-select-sm border shadow-sm rounded-pill px-3 py-1" style="min-width: 150px; cursor: pointer;">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ $kelas_id == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover table-bordered align-middle mb-0 w-100" id="penilaianTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4" style="min-width: 200px;">NAMA SISWA</th>
                            <th class="py-3" style="min-width: 120px;">KELAS</th>
                            <th class="py-3" style="min-width: 150px;">STATUS / WAKTU</th>
                            <th class="py-3" style="min-width: 150px;">JAWABAN</th>
                            <th class="text-center py-3" style="width: 120px;">NILAI</th>
                            <th class="text-center py-3 pe-4" style="width: 100px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
$(document).ready(function() {
    var table = $('#penilaianTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('guru.mapels.tugas.penilaian.data', [$tugas->mapel_id, $tugas->id]) }}",
            data: function (d) {
                d.kelas_id = $('#filterKelas').val();
            }
        },
        columns: [
            { data: 'siswa_info', name: 'nama', className: 'ps-4 align-middle' },
            { data: 'kelas_nama', name: 'kelas.nama_kelas', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'status_waktu', name: 'status_waktu', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'jawaban', name: 'jawaban', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'nilai', name: 'nilai', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center pe-4 align-middle' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 25,
        drawCallback: function(settings) {
            initTinyMCEForModals();
        }
    });

    $('#filterKelas').change(function() {
        table.ajax.reload();
        var kelasId = $(this).val();
        var exportUrl = "{{ route('guru.mapels.tugas.penilaian.export', [$tugas->mapel_id, $tugas->id]) }}";
        if (kelasId) {
            exportUrl += "?kelas_id=" + kelasId;
        }
        $('#btnExportExcel').attr('href', exportUrl);
    });

    // Fix Bootstrap 5 modal focus issue dengan TinyMCE
    document.addEventListener('focusin', (e) => {
        if (e.target.closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
            e.stopImmediatePropagation();
        }
    });

    function initTinyMCEForModals() {
        $('.modal').off('shown.bs.modal hidden.bs.modal');
        $('.modal').on('shown.bs.modal', function() {
            var textarea = $(this).find('textarea.rich-text')[0];
            if (textarea) {
                if (!textarea.id) {
                    textarea.id = 'editor-' + Math.random().toString(36).substr(2, 9);
                }
                tinymce.init({
                    selector: '#' + textarea.id,
                    plugins: 'emoticons link lists',
                    toolbar: 'undo redo | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | emoticons link',
                    menubar: false,
                    height: 200,
                    branding: false,
                    setup: function (editor) {
                        editor.on('change', function () {
                            editor.save();
                        });
                    }
                });
            }
        });

        $('.modal').on('hidden.bs.modal', function() {
            var textarea = $(this).find('textarea.rich-text')[0];
            if (textarea && textarea.id) {
                var editor = tinymce.get(textarea.id);
                if (editor) {
                    editor.remove();
                }
            }
        });
    }
});
</script>
@endsection
