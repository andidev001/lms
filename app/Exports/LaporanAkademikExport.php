<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanAkademikExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $kelas_id;
    protected $mapel_id;

    public function __construct($kelas_id = null, $mapel_id = null)
    {
        $this->kelas_id = $kelas_id;
        $this->mapel_id = $mapel_id;
    }

    public function collection()
    {
        $query = \App\Models\Siswa::with(['kelas', 'progress' => function($q) {
            if ($this->mapel_id) {
                $q->whereHas('materi', function($qM) {
                    $qM->where('mapel_id', $this->mapel_id);
                });
            }
        }]);

        if ($this->kelas_id) {
            $query->where('kelas_id', $this->kelas_id);
        }

        $siswas = $query->get();
        
        $totalMateri = $this->mapel_id ? \App\Models\Materi::where('mapel_id', $this->mapel_id)->count() : \App\Models\Materi::count();
        
        $collection = collect();
        foreach ($siswas as $siswa) {
            $studentTotalMateri = $totalMateri;
            if (!$this->mapel_id && $siswa->kelas) {
                $studentTotalMateri = \App\Models\Materi::whereIn('mapel_id', $siswa->kelas->mapels->pluck('id'))->count();
            }

            $progress = $siswa->progress;
            $avg = $progress->avg('pretest_nilai');
            $completed = $progress->where('is_completed', true)->count();
            
            $percentage = $studentTotalMateri > 0 ? round(($completed / $studentTotalMateri) * 100) : 0;

            $collection->push((object)[
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas ? $siswa->kelas->nama_kelas : '-',
                'completed_count' => $completed,
                'total_materi' => $studentTotalMateri,
                'percentage' => $percentage,
                'avg_nilai' => $avg ? round($avg, 1) : 0,
            ]);
        }
        
        return $collection;
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NIS/NISN',
            'Kelas',
            'Modul Selesai',
            'Total Modul',
            'Persentase Ketuntasan (%)',
            'Nilai Rata-rata Evaluasi'
        ];
    }

    public function map($row): array
    {
        return [
            $row->nama,
            $row->nis,
            $row->kelas,
            $row->completed_count,
            $row->total_materi,
            $row->percentage . '%',
            $row->avg_nilai
        ];
    }
}
