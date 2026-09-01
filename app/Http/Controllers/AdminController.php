<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // For now, only check if logged in. We can add role middleware later.
        $this->middleware('auth');
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalSiswa = \App\Models\Siswa::count();
        $totalGuru = \App\Models\Guru::count();
        $totalKelas = \App\Models\Kelas::count();
        $totalMapel = \App\Models\Mapel::count();
        
        $pengumumans = \App\Models\Pengumuman::with('user')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact('totalSiswa', 'totalGuru', 'totalKelas', 'totalMapel', 'pengumumans'));
    }
}
