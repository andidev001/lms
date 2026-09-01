<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TahunAjaran::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    if($row->is_active){
                        return '<span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle me-1"></i> Aktif</span>';
                    } else {
                        return '<span class="badge bg-secondary rounded-pill px-3">Tidak Aktif</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $modal = '<div class="modal fade text-start" id="editTahunAjaranModal'.$row->id.'" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                  <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                                    <h5 class="modal-title fw-bold">Edit Tahun Ajaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <form method="POST" action="'.route('tahun-ajarans.update', $row->id).'">
                                      '.csrf_field().'
                                      '.method_field('PUT').'
                                      <div class="modal-body p-4">
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Tahun Ajaran</label>
                                              <input type="text" name="nama_tahun" class="form-control form-control-lg rounded-3" value="'.$row->nama_tahun.'" required>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Semester</label>
                                              <select name="semester" class="form-select form-select-lg rounded-3" required>
                                                  <option value="Ganjil" '.($row->semester == 'Ganjil' ? 'selected' : '').'>Ganjil</option>
                                                  <option value="Genap" '.($row->semester == 'Genap' ? 'selected' : '').'>Genap</option>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="modal-footer border-top-0 pb-4 px-4">
                                          <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                                          <button type="submit" class="btn btn-primary rounded-3 px-5">Simpan Perubahan</button>
                                      </div>
                                  </form>
                                </div>
                              </div>
                            </div>';
                            
                    $btn = '<div class="d-flex justify-content-end gap-2">';
                    
                    if(!$row->is_active){
                        $btn .= '<form action="'.route('tahun-ajarans.set-aktif', $row->id).'" method="POST">';
                        $btn .= csrf_field();
                        $btn .= '<button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="return confirm(\'Jadikan Tahun Ajaran ini sebagai yang aktif?\')"><i class="bi bi-power me-1"></i> Set Aktif</button>';
                        $btn .= '</form>';
                    }

                    $btn .= '<button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editTahunAjaranModal'.$row->id.'"><i class="bi bi-pencil-square"></i> Edit</button>';
                    
                    $btn .= '<form action="'.route('tahun-ajarans.destroy', $row->id).'" method="POST">';
                    $btn .= csrf_field();
                    $btn .= method_field('DELETE');
                    $btn .= '<button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')"><i class="bi bi-trash"></i> Hapus</button>';
                    $btn .= '</form>';
                    
                    $btn .= '</div>';
                    
                    return $btn . $modal;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        
        return view('admin.tahun_ajarans.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_tahun' => 'required',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        $count = TahunAjaran::count();
        $is_active = $count == 0 ? true : false; // If first record, make it active

        TahunAjaran::create([
            'nama_tahun' => $request->nama_tahun,
            'semester' => $request->semester,
            'is_active' => $is_active
        ]);

        return redirect()->route('tahun-ajarans.index')
                        ->with('success', 'Tahun Ajaran berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'nama_tahun' => 'required',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update([
            'nama_tahun' => $request->nama_tahun,
            'semester' => $request->semester,
        ]);

        return redirect()->route('tahun-ajarans.index')
                        ->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        if ($tahunAjaran->is_active) {
            return redirect()->route('tahun-ajarans.index')
                        ->with('error', 'Tahun Ajaran yang sedang aktif tidak dapat dihapus.');
        }
        $tahunAjaran->delete();
        
        return redirect()->route('tahun-ajarans.index')
                        ->with('success', 'Tahun Ajaran berhasil dihapus.');
    }

    public function setAktif(string $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        
        // Deactivate all
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        
        // Activate selected
        $tahunAjaran->is_active = true;
        $tahunAjaran->save();

        return redirect()->route('tahun-ajarans.index')
                        ->with('success', 'Tahun Ajaran ' . $tahunAjaran->nama_tahun . ' - ' . $tahunAjaran->semester . ' berhasil diaktifkan.');
    }
}
