<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the siswa dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $siswa = \Illuminate\Support\Facades\Auth::user()->siswa;
        
        $totalMapel = 0;
        $modulSelesai = 0;
        $nilaiRataRata = 0;
        $mapels = collect();

        if ($siswa && $siswa->kelas) {
            $totalMapel = $siswa->kelas->mapels()->count();
            $mapels = $siswa->kelas->mapels;
            
            $modulSelesai = \App\Models\SiswaProgress::where('siswa_id', $siswa->id)
                            ->where('is_completed', true)->count();
                            
            $avg = \App\Models\SiswaProgress::where('siswa_id', $siswa->id)
                            ->whereNotNull('pretest_nilai')->avg('pretest_nilai');
            $nilaiRataRata = $avg ? round($avg, 1) : 0;
        }

        $pengumumans = \App\Models\Pengumuman::where('is_active', true)
                        ->whereIn('target', ['semua', 'siswa'])
                        ->latest()
                        ->take(5)
                        ->get();

        return view('siswa.dashboard', compact('totalMapel', 'modulSelesai', 'nilaiRataRata', 'mapels', 'pengumumans'));
    }
}
