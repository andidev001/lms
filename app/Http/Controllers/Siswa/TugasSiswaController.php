<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasSiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        
        // Ambil semua mapel yang dimiliki siswa ini (melalui kelas)
        $mapels = $siswa->kelas->mapels;
        $mapelIds = $mapels->pluck('id');

        // Ambil tugas dari mapel-mapel tersebut
        $tugases = Tugas::whereIn('mapel_id', $mapelIds)
            ->with(['mapel', 'submissions' => function($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            }])
            ->orderBy('tenggat_waktu', 'asc')
            ->get();

        return view('siswa.tugas.index', compact('tugases'));
    }

    public function show($id)
    {
        $siswa = Auth::user()->siswa;
        $tugas = Tugas::with('mapel')->findOrFail($id);

        // Verifikasi tugas ini milik mapel kelas siswa
        if (!$siswa->kelas->mapels->contains($tugas->mapel_id)) {
            abort(403);
        }

        $submission = TugasSubmission::where('tugas_id', $id)->where('siswa_id', $siswa->id)->first();

        return view('siswa.tugas.show', compact('tugas', 'submission'));
    }

    public function submit(Request $request, $id)
    {
        $siswa = Auth::user()->siswa;
        $tugas = Tugas::findOrFail($id);

        if (!$siswa->kelas->mapels->contains($tugas->mapel_id)) {
            abort(403);
        }

        $request->validate([
            'teks_jawaban' => 'nullable|string',
            'file_jawaban' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar,txt|max:10240',
            'link_jawaban' => 'nullable|url'
        ]);

        $submission = TugasSubmission::where('tugas_id', $id)->where('siswa_id', $siswa->id)->first();

        if ($submission && $submission->nilai !== null) {
            return redirect()->back()->with('error', 'Tugas ini sudah dinilai dan tidak dapat diubah lagi.');
        }

        $path = $submission->file_jawaban ?? null;
        if ($request->hasFile('file_jawaban')) {
            $path = $request->file('file_jawaban')->store('tugas_jawaban', 'public');
        }

        TugasSubmission::updateOrCreate(
            ['tugas_id' => $id, 'siswa_id' => $siswa->id],
            [
                'teks_jawaban' => $request->teks_jawaban,
                'file_jawaban' => $path,
                'link_jawaban' => $request->link_jawaban,
                'waktu_pengumpulan' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}
