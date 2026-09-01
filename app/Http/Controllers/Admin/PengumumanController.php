<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = \App\Models\Pengumuman::with('user')->latest();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('target_badge', function($row){
                    if($row->target == 'semua') return '<span class="badge bg-primary">Semua</span>';
                    if($row->target == 'guru') return '<span class="badge bg-info text-dark">Guru</span>';
                    return '<span class="badge bg-warning text-dark">Siswa</span>';
                })
                ->addColumn('status_badge', function($row){
                    if($row->is_active) {
                        return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i> Nonaktif</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="d-flex gap-2">';
                    $btn .= '<a href="'.route('pengumumans.edit', $row->id).'" class="btn btn-sm btn-light border text-primary"><i class="bi bi-pencil-square"></i> Edit</a>';
                    
                    $btn .= '<form action="'.route('pengumumans.destroy', $row->id).'" method="POST" class="d-inline">';
                    $btn .= csrf_field();
                    $btn .= method_field('DELETE');
                    $btn .= '<button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm(\'Hapus pengumuman ini?\')"><i class="bi bi-trash"></i> Hapus</button>';
                    $btn .= '</form>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['target_badge', 'status_badge', 'action'])
                ->make(true);
        }
        
        return view('admin.pengumumans.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pengumumans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'target' => 'required|in:semua,guru,siswa',
            'is_active' => 'boolean'
        ]);

        \App\Models\Pengumuman::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'target' => $request->target,
            'is_active' => $request->has('is_active') ? true : false,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        return redirect()->route('pengumumans.index')->with('success', 'Pengumuman berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengumuman = \App\Models\Pengumuman::findOrFail($id);
        return view('admin.pengumumans.edit', compact('pengumuman'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'target' => 'required|in:semua,guru,siswa',
        ]);

        $pengumuman = \App\Models\Pengumuman::findOrFail($id);
        $pengumuman->update([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'target' => $request->target,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('pengumumans.index')->with('success', 'Pengumuman berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengumuman = \App\Models\Pengumuman::findOrFail($id);
        $pengumuman->delete();

        return redirect()->route('pengumumans.index')->with('success', 'Pengumuman berhasil dihapus');
    }
}
