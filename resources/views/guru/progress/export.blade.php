<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="{{ $materis->count() + $tugases->count() + 4 }}" style="font-weight: bold; text-align: center; font-size: 14px;">
                    Daftar Kehadiran &amp; Rekap Nilai
                </th>
            </tr>
            <tr>
                <th colspan="{{ $materis->count() + $tugases->count() + 4 }}" style="text-align: center;">
                    Mata Pelajaran: {{ $mapel->nama_mapel }} | Kelas: {{ $kelas->nama_kelas }}
                </th>
            </tr>
            <tr>
                <th colspan="{{ $materis->count() + $tugases->count() + 4 }}"></th>
            </tr>
            <tr>
                <th style="font-weight: bold; text-align: center;">No</th>
                <th style="font-weight: bold;">Nama Siswa</th>
                <th style="font-weight: bold;">NIS/NISN</th>
                @foreach($materis as $materi)
                    <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #d1d5db; text-align: center;">Modul {{ $materi->urutan }} ({{ $materi->judul }})</th>
                @endforeach
                @foreach($tugases as $index => $tugas)
                    <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #d1d5db; text-align: center;">Tugas {{ $index + 1 }} ({{ $tugas->judul }})</th>
                @endforeach
                <th style="font-weight: bold; background-color: #d1fae5; border: 1px solid #d1d5db; text-align: center;">Rata-rata Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $index => $siswa)
                @php
                    $totalNilai = 0;
                    $countPretest = 0;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $siswa->nama }}</td>
                    <td>{{ $siswa->nis }}</td>
                    
                    @foreach($materis as $materi)
                        @php
                            $progress = $progressMatrix[$siswa->id][$materi->id] ?? null;
                            $hasPretest = $materi->pretest_questions()->count() > 0;
                            $status = 'Belum Hadir';
                            
                            if ($progress) {
                                if ($progress->is_completed) {
                                    if ($hasPretest && $progress->pretest_nilai !== null) {
                                        $status = $progress->pretest_nilai; // Show grade if pretest exists
                                        $totalNilai += $progress->pretest_nilai;
                                        $countPretest++;
                                    } else {
                                        $status = 'Hadir & Selesai';
                                    }
                                } elseif ($progress->pdf_dibaca || $progress->video_ditonton || !empty($progress->cerita_reflektif)) {
                                    $status = 'Hadir (Proses)';
                                }
                            }
                        @endphp
                        <td style="text-align: center;">{{ $status }}</td>
                    @endforeach

                    <!-- Kolom Tugas -->
                    @foreach($tugases as $tugas)
                        @php
                            $submission = $tugasMatrix[$siswa->id][$tugas->id] ?? null;
                        @endphp
                        <td style="border: 1px solid #d1d5db; text-align: center;">
                            @if($submission)
                                @if($submission->nilai !== null)
                                    @php
                                        $totalNilai += $submission->nilai;
                                        $countPretest++;
                                    @endphp
                                    {{ $submission->nilai }}
                                @else
                                    Perlu Dinilai
                                @endif
                            @else
                                @if($tugas->tenggat_waktu < now())
                                    Tidak Kumpul
                                @else
                                    Menunggu
                                @endif
                            @endif
                        </td>
                    @endforeach
                    
                    <td style="border: 1px solid #d1d5db; text-align: center; font-weight: bold;">
                        @if($countPretest > 0)
                            {{ round($totalNilai / $countPretest) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
