<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->guru;
        
        // Ensure guru exists
        if (!$guru) {
            return view('guru.dashboard', ['mapels' => []])->with('error', 'Profil Guru tidak ditemukan.');
        }

        $mapels = $guru->mapels()->with('kelas')->get();
        $totalMateri = \App\Models\Materi::whereIn('mapel_id', $mapels->pluck('id'))->count();
        
        $pengumumans = \App\Models\Pengumuman::where('is_active', true)
                        ->whereIn('target', ['semua', 'guru'])
                        ->latest()
                        ->take(5)
                        ->get();
        
        return view('guru.dashboard', compact('mapels', 'totalMateri', 'pengumumans'));
    }
}
