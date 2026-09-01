@extends('layouts.siswa')

@section('title', 'Tugas Saya')
@section('subtitle', 'Daftar penugasan yang harus Anda kerjakan')

@section('content')
<div class="row g-4">
    @forelse($tugases as $tugas)
        @php
            $submission = $tugas->submissions->first();
            $isLate = $submission ? $submission->waktu_pengumpulan > $tugas->tenggat_waktu : now() > $tugas->tenggat_waktu;
            $status = 'Belum Dikerjakan';
            $statusColor = 'warning';
            $statusIcon = 'bi-exclamation-circle';

            if ($submission) {
                if ($submission->nilai !== null) {
                    $status = 'Sudah Dinilai (' . $submission->nilai . ')';
                    $statusColor = 'success';
                    $statusIcon = 'bi-check-circle-fill';
                } else {
                    $status = 'Menunggu Penilaian';
                    $statusColor = 'info';
                    $statusIcon = 'bi-clock-history';
                }
            } else if ($isLate) {
                $status = 'Terlambat';
                $statusColor = 'danger';
                $statusIcon = 'bi-x-circle';
            }
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative transition-hover" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary border rounded-pill px-3 py-2 fw-semibold">
                            <i class="bi bi-book me-1"></i> {{ $tugas->mapel->nama_mapel }}
                        </span>
                        <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} border rounded-pill px-3 py-2">
                            <i class="bi {{ $statusIcon }} me-1"></i> {{ $status }}
                        </span>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-2 mt-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $tugas->judul }}
                    </h5>
                    
                    <div class="text-muted small mb-4 mt-3 bg-light p-3 rounded-3 border d-flex align-items-center">
                        <i class="bi bi-calendar-event text-danger fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold text-dark">Tenggat Waktu:</div>
                            <div class="{{ $isLate && !$submission ? 'text-danger fw-bold' : '' }}">
                                {{ $tugas->tenggat_waktu->format('l, d F Y - H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('siswa.tugas.show', $tugas->id) }}" class="btn btn-outline-primary w-100 rounded-pill fw-semibold py-2">
                        @if($submission)
                            Lihat Jawaban
                        @else
                            Kerjakan Sekarang
                        @endif
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <img src="https://illustrations.popsy.co/amber/student-going-to-school.svg" alt="Hore" style="height: 250px;" class="mb-4 opacity-75">
            <h4 class="fw-bold text-dark mb-2">Hore! Tidak ada tugas.</h4>
            <p class="text-muted fs-5">Anda sudah menyelesaikan semua tugas, atau guru belum memberikan tugas baru.</p>
        </div>
    @endforelse
</div>
@endsection
