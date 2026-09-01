@extends('layouts.guru')

@section('title', 'Kehadiran & Gradebook')
@section('subtitle', 'Rekap Nilai ' . $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('guru.progress.index') }}" class="btn btn-light rounded-pill px-4 border shadow-sm fw-semibold">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('guru.progress.export', [$mapel->id, $kelas->id]) }}" class="btn btn-success rounded-pill px-4 shadow-sm">
            <i class="bi bi-file-earmark-excel me-2"></i> Ekspor Excel
        </a>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-2"></i> Cetak Rekap
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 print-container">
    <div class="card-header bg-white border-bottom p-4">
        <h5 class="fw-bold mb-1">Daftar Kehadiran & Buku Nilai</h5>
        <p class="text-muted small mb-0">{{ $mapel->nama_mapel }} | Kelas: {{ $kelas->nama_kelas }} | Total Siswa: {{ $siswas->count() }}</p>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="table table-hover table-bordered align-middle mb-0 w-100" id="progressTable" style="min-width: 800px;">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center py-3" style="width: 50px;">No</th>
                        <th class="py-3" style="min-width: 200px;">Nama Siswa</th>
                        @foreach($materis as $materi)
                            <th class="text-center py-3" style="min-width: 150px;">
                                <div class="small text-muted mb-1">Modul {{ $materi->urutan }}</div>
                                <div class="text-dark">{{ Str::limit($materi->judul, 20) }}</div>
                            </th>
                        @endforeach
                        @foreach($tugases as $index => $tugas)
                            <th class="text-center py-3" style="min-width: 150px;">
                                <div class="small text-muted mb-1">Tugas {{ $index + 1 }}</div>
                                <div class="text-dark">{{ Str::limit($tugas->judul, 20) }}</div>
                            </th>
                        @endforeach
                        <th class="text-center py-3 bg-success bg-opacity-10" style="min-width: 100px;">
                            Rata-rata Akhir
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-container, .print-container * {
        visibility: visible;
    }
    .print-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .d-print-none {
        display: none !important;
    }
}
</style>
</style>

<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#progressTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('guru.progress.data', [$mapel->id, $kelas->id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted align-middle' },
            { data: 'siswa_info', name: 'siswa_info', className: 'align-middle' },
            @foreach($materis as $materi)
                { data: 'materi_{{ $materi->id }}', name: 'materi_{{ $materi->id }}', orderable: false, searchable: false, className: 'text-center align-middle' },
            @endforeach
            @foreach($tugases as $tugas)
                { data: 'tugas_{{ $tugas->id }}', name: 'tugas_{{ $tugas->id }}', orderable: false, searchable: false, className: 'text-center align-middle' },
            @endforeach
            { data: 'rata_rata', name: 'rata_rata', orderable: false, searchable: false, className: 'text-center bg-success bg-opacity-10 align-middle' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 25,
        scrollX: true
    });
});
</script>
@endsection
