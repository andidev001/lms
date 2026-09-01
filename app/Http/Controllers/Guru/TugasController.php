<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->guru;
        $mapels = $guru->mapels()->with('kelas')->get();
        return view('guru.tugas.index', compact('mapels'));
    }

    public function show($mapel_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) abort(403);
        $tugases = $mapel->tugas()->with('materi')->orderBy('tenggat_waktu', 'desc')->get();
        $materis = $mapel->materis()->orderBy('urutan')->get();
        return view('guru.tugas.list', compact('mapel', 'tugases', 'materis'));
    }

    public function create($mapel_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) abort(403);
        $materis = $mapel->materis()->orderBy('urutan')->get();
        return view('guru.tugas.create', compact('mapel', 'materis'));
    }

    public function store(Request $request, $mapel_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) abort(403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'tenggat_waktu' => 'required|date',
            'file_lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar,txt|max:10240'
        ]);

        $path = null;
        if ($request->hasFile('file_lampiran')) {
            $path = $request->file('file_lampiran')->store('tugas_lampiran', 'public');
        }

        Tugas::create([
            'mapel_id' => $mapel->id,
            'materi_id' => $request->materi_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tenggat_waktu' => $request->tenggat_waktu,
            'file_lampiran' => $path
        ]);

        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function edit($mapel_id, $id)
    {
        $tugas = Tugas::findOrFail($id);
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) abort(403);
        $materis = $mapel->materis()->orderBy('urutan')->get();

        return view('guru.tugas.edit', compact('tugas', 'mapel', 'materis'));
    }

    public function update(Request $request, $mapel_id, $id)
    {
        $tugas = Tugas::findOrFail($id);
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) abort(403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'tenggat_waktu' => 'required|date',
            'file_lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar,txt|max:10240'
        ]);

        if ($request->hasFile('file_lampiran')) {
            $path = $request->file('file_lampiran')->store('tugas_lampiran', 'public');
            $tugas->file_lampiran = $path;
        }

        $tugas->update([
            'materi_id' => $request->materi_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tenggat_waktu' => $request->tenggat_waktu,
        ]);

        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil diperbarui!');
    }

    public function destroy($mapel_id, $id)
    {
        $tugas = Tugas::findOrFail($id);
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) abort(403);

        $tugas->delete();
        return redirect()->back()->with('success', 'Tugas berhasil dihapus!');
    }
}
