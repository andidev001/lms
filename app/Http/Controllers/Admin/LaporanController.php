<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function akademik(Request $request)
    {
        $kelas = \App\Models\Kelas::all();
        $mapels = \App\Models\Mapel::all();

        $selectedKelas = $request->get('kelas_id');
        $selectedMapel = $request->get('mapel_id');

        $query = \App\Models\Siswa::with(['kelas', 'progress' => function($q) use ($selectedMapel) {
            if ($selectedMapel) {
                $q->whereHas('materi', function($qM) use ($selectedMapel) {
                    $qM->where('mapel_id', $selectedMapel);
                });
            }
        }]);

        if ($selectedKelas) {
            $query->where('kelas_id', $selectedKelas);
        }

        $siswas = $query->get();

        // Get total materi for calculation
        if ($selectedMapel) {
            $totalMateri = \App\Models\Materi::where('mapel_id', $selectedMapel)->count();
        } else {
            $totalMateri = \App\Models\Materi::count(); // Overall total
        }

        // We will process the average and completion in the view or here. Let's do it here.
        $laporanData = [];
        foreach ($siswas as $siswa) {
            // Check if we need to adjust totalMateri based on student's actual mapels if no mapel is selected
            $studentTotalMateri = $totalMateri;
            if (!$selectedMapel && $siswa->kelas) {
                $studentTotalMateri = \App\Models\Materi::whereIn('mapel_id', $siswa->kelas->mapels->pluck('id'))->count();
            }

            $progress = $siswa->progress;
            $avg = $progress->avg('pretest_nilai');
            $completed = $progress->where('is_completed', true)->count();
            
            $percentage = $studentTotalMateri > 0 ? round(($completed / $studentTotalMateri) * 100) : 0;

            $laporanData[] = (object) [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas ? $siswa->kelas->nama_kelas : '-',
                'completed_count' => $completed,
                'total_materi' => $studentTotalMateri,
                'percentage' => $percentage,
                'avg_nilai' => $avg ? round($avg, 1) : 0,
            ];
        }

        return view('admin.laporans.akademik', compact('kelas', 'mapels', 'selectedKelas', 'selectedMapel', 'laporanData'));
    }

    public function exportAkademik(Request $request)
    {
        $selectedKelas = $request->get('kelas_id');
        $selectedMapel = $request->get('mapel_id');
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanAkademikExport($selectedKelas, $selectedMapel),
            'Laporan_Akademik_Siswa_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
