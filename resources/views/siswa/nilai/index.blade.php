@extends('layouts.siswa')

@section('title', 'Rapor Digital & Nilai')

@section('content')
<div class="row mb-5">
    <!-- Header Rapor -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden" style="background-color: #8b5cf6; background-image: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%);">
            <!-- Dekorasi -->
            <div class="position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(16,185,129,0.2) 0%, rgba(16,185,129,0) 70%); border-radius: 50%;"></div>
            
            <div class="card-body p-4 p-lg-5 text-white position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-lg-7 mb-4 mb-lg-0">
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 py-2 rounded-pill mb-3 fw-semibold">
                            <i class="bi bi-person-check me-1"></i> Rapor Pembelajaran
                        </span>
                        <h2 class="fw-bold mb-2 text-white">Halo, {{ Auth::user()->name }}!</h2>
                        <p class="text-white-50 fs-5 mb-0">Ini adalah rekapitulasi nilai dan perkembangan belajar Anda sejauh ini.</p>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-flex justify-content-lg-end gap-3">
                            <div class="text-center p-3 rounded-4" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); flex: 1; max-width: 140px;">
                                <div class="text-white-50 small text-uppercase fw-bold mb-1">Rata-Rata</div>
                                <div class="fs-1 fw-bold text-success">{{ $rataKeseluruhan }}</div>
                            </div>
                            <div class="text-center p-3 rounded-4" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); flex: 1; max-width: 140px;">
                                <div class="text-white-50 small text-uppercase fw-bold mb-1">Total Tugas</div>
                                <div class="fs-2 fw-bold text-white mt-1">{{ $totalTugasDinilai }}</div>
                            </div>
                            <div class="text-center p-3 rounded-4" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); flex: 1; max-width: 140px;">
                                <div class="text-white-50 small text-uppercase fw-bold mb-1">Total Kuis</div>
                                <div class="fs-2 fw-bold text-white mt-1">{{ $totalKuisDikerjakan }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Mapel (Accordion) -->
<h5 class="fw-bold text-dark mb-4"><i class="bi bi-journals me-2 text-primary"></i> Detail per Mata Pelajaran</h5>

<div class="accordion accordion-flush bg-white rounded-4 shadow-sm border border-light overflow-hidden" id="accordionMapel">
    @forelse($rekapMapel as $index => $item)
        @php
            $mapel = $item['mapel'];
            $rataRata = $item['rata_rata'];
            
            // Tentukan warna progress bar berdasarkan nilai
            $colorClass = 'danger';
            if ($rataRata >= 85) $colorClass = 'success';
            elseif ($rataRata >= 70) $colorClass = 'primary';
            elseif ($rataRata >= 50) $colorClass = 'warning';
        @endphp
        
        <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header" id="heading-{{ $index }}">
                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }} p-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $index }}">
                    <div class="d-flex flex-column flex-md-row w-100 pe-3 align-items-md-center">
                        <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-book fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">{{ $mapel->nama_mapel }}</h6>
                                <span class="small text-muted">Guru: {{ $mapel->guru->user->name ?? 'Anonim' }}</span>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center flex-grow-1" style="max-width: 300px;">
                            <div class="me-3 text-end">
                                <div class="fw-bold text-{{ $colorClass }} fs-5" style="line-height: 1;">{{ $rataRata }}</div>
                                <span class="small text-muted">Rata-rata</span>
                            </div>
                            <div class="progress flex-grow-1" style="height: 10px;">
                                <div class="progress-bar bg-{{ $colorClass }}" role="progressbar" style="width: {{ $rataRata }}%;" aria-valuenow="{{ $rataRata }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </button>
            </h2>
            <div id="collapse-{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading-{{ $index }}" data-bs-parent="#accordionMapel">
                <div class="accordion-body p-4 bg-light bg-opacity-50">
                    
                    <div class="row g-4">
                        <!-- Tabel Nilai Tugas -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100 rounded-4">
                                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-journal-check text-primary me-2"></i> Nilai Tugas</h6>
                                </div>
                                <div class="card-body">
                                    @if($item['tugas_submissions']->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-borderless align-middle mb-0">
                                                <tbody>
                                                    @foreach($item['tugas_submissions'] as $submission)
                                                        <tr class="border-bottom">
                                                            <td class="ps-0 py-3">
                                                                <div class="fw-semibold text-dark">{{ $submission->tugas->judul }}</div>
                                                                @if($submission->catatan_guru)
                                                                    <div class="small text-muted mt-1 fst-italic">
                                                                        "{{ $submission->catatan_guru }}"
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="text-end pe-0 py-3">
                                                                <span class="badge bg-success bg-opacity-10 text-success fs-6 border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                                                    {{ $submission->nilai }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted small">
                                            <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
                                            Belum ada tugas yang dinilai.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Nilai Kuis -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100 rounded-4">
                                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-patch-question text-warning me-2"></i> Nilai Kuis / Pre-Test</h6>
                                </div>
                                <div class="card-body">
                                    @if($item['kuis_progress']->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-borderless align-middle mb-0">
                                                <tbody>
                                                    @foreach($item['kuis_progress'] as $progress)
                                                        <tr class="border-bottom">
                                                            <td class="ps-0 py-3">
                                                                <div class="fw-semibold text-dark">Kuis Modul {{ $progress->materi->urutan }}</div>
                                                                <div class="small text-muted mt-1 text-truncate" style="max-width: 200px;">
                                                                    {{ $progress->materi->judul }}
                                                                </div>
                                                            </td>
                                                            <td class="text-end pe-0 py-3">
                                                                <span class="badge bg-primary bg-opacity-10 text-primary fs-6 border border-primary border-opacity-25 px-3 py-2 rounded-pill">
                                                                    {{ $progress->pretest_nilai }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted small">
                                            <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
                                            Belum ada kuis yang diselesaikan.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> <!-- End row g-4 -->

                </div>
            </div>
        </div>
    @empty
        <div class="p-5 text-center">
            <img src="https://illustrations.popsy.co/amber/student-going-to-school.svg" alt="Empty" style="height: 180px;" class="mb-4 opacity-75">
            <h5 class="fw-bold text-dark">Belum Ada Data Nilai</h5>
            <p class="text-muted">Nilai Anda akan muncul di sini setelah Anda menyelesaikan tugas atau kuis dan guru telah menilainya.</p>
        </div>
    @endforelse
</div>

@endsection
