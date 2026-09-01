<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SiswaProgress;
use App\Models\TugasSubmission;

class NilaiController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        
        if (!$siswa || !$siswa->kelas) {
            return view('siswa.nilai.index')->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        $mapels = $siswa->kelas->mapels()->with(['materis', 'tugas'])->get();

        $rekapMapel = [];
        $totalNilaiKeseluruhan = 0;
        $totalKomponenDinilai = 0;
        
        $totalTugasDinilai = 0;
        $totalKuisDikerjakan = 0;

        foreach ($mapels as $mapel) {
            // 1. Ambil Nilai Tugas
            $tugasIds = $mapel->tugas->pluck('id');
            $tugasSubmissions = TugasSubmission::where('siswa_id', $siswa->id)
                                               ->whereIn('tugas_id', $tugasIds)
                                               ->whereNotNull('nilai')
                                               ->with('tugas')
                                               ->get();
            
            $totalNilaiTugas = $tugasSubmissions->sum('nilai');
            $countTugas = $tugasSubmissions->count();
            $totalTugasDinilai += $countTugas;

            // 2. Ambil Nilai Kuis (Pretest/Posttest)
            $materiIds = $mapel->materis->pluck('id');
            $kuisProgress = SiswaProgress::where('siswa_id', $siswa->id)
                                         ->whereIn('materi_id', $materiIds)
                                         ->whereNotNull('pretest_nilai')
                                         ->with('materi')
                                         ->get();
            
            $totalNilaiKuis = $kuisProgress->sum('pretest_nilai');
            $countKuis = $kuisProgress->count();
            $totalKuisDikerjakan += $countKuis;

            // Kalkulasi Rata-rata per Mapel
            $totalSkorMapel = $totalNilaiTugas + $totalNilaiKuis;
            $totalKomponenMapel = $countTugas + $countKuis;
            
            $rataMapel = $totalKomponenMapel > 0 ? round($totalSkorMapel / $totalKomponenMapel) : 0;

            // Tambahkan ke total keseluruhan
            $totalNilaiKeseluruhan += $totalSkorMapel;
            $totalKomponenDinilai += $totalKomponenMapel;

            $rekapMapel[] = [
                'mapel' => $mapel,
                'rata_rata' => $rataMapel,
                'tugas_submissions' => $tugasSubmissions,
                'kuis_progress' => $kuisProgress,
            ];
        }

        $rataKeseluruhan = $totalKomponenDinilai > 0 ? round($totalNilaiKeseluruhan / $totalKomponenDinilai) : 0;

        return view('siswa.nilai.index', compact(
            'rekapMapel',
            'rataKeseluruhan',
            'totalTugasDinilai',
            'totalKuisDikerjakan'
        ));
    }
}
