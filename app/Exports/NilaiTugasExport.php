<?php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\Tugas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NilaiTugasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tugas_id;
    protected $kelas_id;

    public function __construct($tugas_id, $kelas_id = null)
    {
        $this->tugas_id = $tugas_id;
        $this->kelas_id = $kelas_id;
    }

    public function collection()
    {
        $tugas = Tugas::findOrFail($this->tugas_id);

        $query = Siswa::whereHas('kelas', function($query) use ($tugas) {
            $query->whereHas('mapels', function($q) use ($tugas) {
                $q->where('mapels.id', $tugas->mapel_id);
            });
        })->with(['kelas', 'tugas_submissions' => function($q) {
            $q->where('tugas_id', $this->tugas_id);
        }]);

        if ($this->kelas_id) {
            $query->where('kelas_id', $this->kelas_id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NIS',
            'NAMA SISWA',
            'KELAS',
            'STATUS',
            'WAKTU PENGUMPULAN',
            'NILAI',
            'CATATAN GURU'
        ];
    }

    public function map($siswa): array
    {
        $submission = $siswa->tugas_submissions->first();
        $tugas = Tugas::find($this->tugas_id);

        $status = 'Belum Mengumpulkan';
        $waktu = '-';
        $nilai = '0';
        $catatan = '-';

        if ($submission) {
            $status = $submission->waktu_pengumpulan > $tugas->tenggat_waktu ? 'Terlambat' : 'Tepat Waktu';
            $waktu = \Carbon\Carbon::parse($submission->waktu_pengumpulan)->format('d-m-Y H:i:s');
            $nilai = $submission->nilai !== null ? $submission->nilai : 'Belum Dinilai';
            $catatan = $submission->catatan_guru ?? '-';
        }

        return [
            $siswa->nis,
            $siswa->nama,
            $siswa->kelas->nama_kelas ?? '-',
            $status,
            $waktu,
            $nilai,
            $catatan
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
