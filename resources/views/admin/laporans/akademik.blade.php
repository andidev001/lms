@extends('layouts.admin')

@section('title', 'Laporan Akademik Siswa')
@section('subtitle', 'Pantau nilai rata-rata dan tingkat ketuntasan modul siswa')

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-funnel text-primary me-2"></i>Filter Laporan</h6>
        <form action="{{ route('laporans.akademik') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-muted small">Kelas</label>
                <select name="kelas_id" class="form-select rounded-3">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelas == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small">Mata Pelajaran</label>
                <select name="mapel_id" class="form-select rounded-3">
                    <option value="">-- Semua Mata Pelajaran --</option>
                    @foreach($mapels as $m)
                        <option value="{{ $m->id }}" {{ $selectedMapel == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4 me-2">
                    <i class="bi bi-search me-1"></i> Tampilkan
                </button>
                <a href="{{ route('laporans.akademik') }}" class="btn btn-light rounded-pill px-4 border">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Hasil Laporan</h5>
        <a href="{{ route('laporans.akademik.export', ['kelas_id' => $selectedKelas, 'mapel_id' => $selectedMapel]) }}" class="btn btn-success rounded-pill px-4 shadow-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel
        </a>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-borderless border" id="laporanTable">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3">No</th>
                        <th class="py-3">Nama Siswa</th>
                        <th class="py-3">NIS/NISN</th>
                        <th class="py-3">Kelas</th>
                        <th class="py-3 text-center">Modul Selesai</th>
                        <th class="py-3 text-center">Ketuntasan (%)</th>
                        <th class="py-3 text-center pe-3">Nilai Rata-Rata</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporanData as $index => $row)
                        <tr>
                            <td class="ps-3">{{ $index + 1 }}</td>
                            <td class="fw-semibold text-dark">{{ $row->nama }}</td>
                            <td>{{ $row->nis }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $row->kelas }}</span></td>
                            <td class="text-center">
                                <span class="fw-bold">{{ $row->completed_count }}</span> / {{ $row->total_materi }}
                            </td>
                            <td class="text-center">
                                @php
                                    $progressColor = 'bg-danger';
                                    if($row->percentage >= 50) $progressColor = 'bg-warning';
                                    if($row->percentage >= 80) $progressColor = 'bg-success';
                                @endphp
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress" style="width: 60px; height: 6px;">
                                        <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $row->percentage }}%"></div>
                                    </div>
                                    <span class="small fw-semibold">{{ $row->percentage }}%</span>
                                </div>
                            </td>
                            <td class="text-center pe-3">
                                @php
                                    $nilaiColor = 'text-danger';
                                    if($row->avg_nilai >= 60) $nilaiColor = 'text-warning';
                                    if($row->avg_nilai >= 80) $nilaiColor = 'text-success';
                                @endphp
                                <span class="fw-bold {{ $nilaiColor }} fs-5">{{ $row->avg_nilai }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 mb-2 d-block"></i>
                                Tidak ada data siswa yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#laporanTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            }
        });
    });
</script>
@endpush
