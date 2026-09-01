<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($mapel_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        
        // Pastikan mapel ini milik guru yang sedang login
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403, 'Unauthorized access.');
        }

        $materis = Materi::where('mapel_id', $mapel_id)->orderBy('urutan', 'asc')->get();
        return view('guru.materi.index', compact('mapel', 'materis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($mapel_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }
        return view('guru.materi.create', compact('mapel'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $mapel_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }

        $this->validate($request, [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file_pdf' => 'nullable|mimes:pdf|max:10240', // max 10MB
            'url_youtube' => 'nullable|url',
        ]);

        // Cari urutan terakhir
        $lastUrutan = Materi::where('mapel_id', $mapel_id)->max('urutan') ?? 0;

        $materi = new Materi();
        $materi->mapel_id = $mapel_id;
        $materi->judul = $request->judul;
        $materi->deskripsi = $request->deskripsi;
        $materi->urutan = $lastUrutan + 1;
        $materi->url_youtube = $request->url_youtube;

        if ($request->hasFile('file_pdf')) {
            $pdfPath = $request->file('file_pdf')->store('materi_pdfs', 'public');
            $materi->file_pdf = $pdfPath;
        }

        $materi->save();

        return redirect()->route('guru.mapels.materis.index', $mapel_id)
                        ->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($mapel_id, $id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }

        $materi = Materi::with('pretest_questions')->findOrFail($id);
        
        return view('guru.materi.show', compact('mapel', 'materi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($mapel_id, $id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }

        $materi = Materi::findOrFail($id);
        return view('guru.materi.edit', compact('mapel', 'materi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $mapel_id, $id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }

        $this->validate($request, [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file_pdf' => 'nullable|mimes:pdf|max:10240',
            'url_youtube' => 'nullable|url',
        ]);

        $materi = Materi::findOrFail($id);
        $materi->judul = $request->judul;
        $materi->deskripsi = $request->deskripsi;
        $materi->url_youtube = $request->url_youtube;

        if ($request->hasFile('file_pdf')) {
            // Hapus file lama jika ada
            if ($materi->file_pdf && Storage::disk('public')->exists($materi->file_pdf)) {
                Storage::disk('public')->delete($materi->file_pdf);
            }
            $pdfPath = $request->file('file_pdf')->store('materi_pdfs', 'public');
            $materi->file_pdf = $pdfPath;
        }

        $materi->save();

        return redirect()->route('guru.mapels.materis.index', $mapel_id)
                        ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($mapel_id, $id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }

        $materi = Materi::findOrFail($id);
        
        if ($materi->file_pdf && Storage::disk('public')->exists($materi->file_pdf)) {
            Storage::disk('public')->delete($materi->file_pdf);
        }
        
        $materi->delete();

        // Re-order urutan
        $materis = Materi::where('mapel_id', $mapel_id)->orderBy('urutan', 'asc')->get();
        foreach ($materis as $index => $m) {
            $m->urutan = $index + 1;
            $m->save();
        }

        return redirect()->route('guru.mapels.materis.index', $mapel_id)
                        ->with('success', 'Materi berhasil dihapus.');
    }
}
