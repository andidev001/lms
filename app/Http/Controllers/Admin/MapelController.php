<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\Guru;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Mapel::with('guru_pengampu')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('guru_pengampu', function($row){
                    if ($row->guru_pengampu) {
                        return '<div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <span class="fw-bold text-primary" style="font-size: 0.8rem;">'.substr($row->guru_pengampu->nama, 0, 1).'</span>
                                    </div>
                                    '.$row->guru_pengampu->nama.'
                                </div>';
                    }
                    return '<span class="text-muted fst-italic">Belum ditentukan</span>';
                })
                ->addColumn('kategori_badge', function($row){
                    if ($row->kategori) {
                        return '<span class="badge bg-secondary rounded-pill px-3 py-2 fw-normal">'.$row->kategori.'</span>';
                    }
                    return '-';
                })
                ->addColumn('action', function($row){
                    $gurus = Guru::orderBy('nama', 'asc')->get();
                    $guruOptions = '<option value="">-- Pilih Guru Pengampu --</option>';
                    foreach ($gurus as $guru) {
                        $selected = $row->guru_id == $guru->id ? 'selected' : '';
                        $guruOptions .= '<option value="'.$guru->id.'" '.$selected.'>'.$guru->nama.'</option>';
                    }

                    $kategoriOptions = '';
                    $kategoris = ['Muatan Nasional', 'Muatan Kewilayahan', 'Muatan Peminatan Kejuruan', 'Lintas Minat'];
                    foreach ($kategoris as $kat) {
                        $selected = $row->kategori == $kat ? 'selected' : '';
                        $kategoriOptions .= '<option value="'.$kat.'" '.$selected.'>'.$kat.'</option>';
                    }

                    $modal = '<div class="modal fade text-start" id="editMapelModal'.$row->id.'" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                  <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                                    <h5 class="modal-title fw-bold">Edit Mata Pelajaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <form method="POST" action="'.route('mapels.update', $row->id).'">
                                      '.csrf_field().'
                                      '.method_field('PUT').'
                                      <div class="modal-body p-4">
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Kode Mapel</label>
                                              <input type="text" name="kode_mapel" class="form-control form-control-lg rounded-3" value="'.$row->kode_mapel.'" required>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Nama Mapel</label>
                                              <input type="text" name="nama_mapel" class="form-control form-control-lg rounded-3" value="'.$row->nama_mapel.'" required>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Kategori</label>
                                              <select name="kategori" class="form-select form-select-lg rounded-3">
                                                  <option value="">-- Pilih Kategori --</option>
                                                  '.$kategoriOptions.'
                                              </select>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Guru Pengampu (Opsional)</label>
                                              <select name="guru_id" class="form-select form-select-lg rounded-3">
                                                  '.$guruOptions.'
                                              </select>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Keterangan</label>
                                              <textarea name="keterangan" class="form-control rounded-3" rows="3">'.$row->keterangan.'</textarea>
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

                    $duplicateModal = '<div class="modal fade text-start" id="duplicateMapelModal'.$row->id.'" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                  <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                                    <h5 class="modal-title fw-bold">Tambah Guru untuk Mapel Ini</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <form method="POST" action="'.route('mapels.store').'">
                                      '.csrf_field().'
                                      <input type="hidden" name="kode_mapel" value="'.$row->kode_mapel.'">
                                      <input type="hidden" name="nama_mapel" value="'.$row->nama_mapel.'">
                                      <input type="hidden" name="kategori" value="'.$row->kategori.'">
                                      <input type="hidden" name="keterangan" value="'.$row->keterangan.'">
                                      <div class="modal-body p-4">
                                          <div class="alert alert-info rounded-3 mb-4">
                                              Anda akan menambahkan guru pengampu baru untuk mapel <strong>'.$row->nama_mapel.'</strong> ('.$row->kode_mapel.').
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Pilih Guru Pengampu Baru</label>
                                              <select name="guru_id" class="form-select form-select-lg rounded-3" required>
                                                  '.$guruOptions.'
                                              </select>
                                          </div>
                                      </div>
                                      <div class="modal-footer border-top-0 pb-4 px-4">
                                          <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                                          <button type="submit" class="btn btn-primary rounded-3 px-5">Tambahkan</button>
                                      </div>
                                  </form>
                                </div>
                              </div>
                            </div>';
                            
                    $btn = '<form action="'.route('mapels.destroy', $row->id).'" method="POST" class="d-flex justify-content-end gap-2">';
                    $btn .= '<button type="button" class="btn btn-sm btn-light border text-success" data-bs-toggle="modal" data-bs-target="#duplicateMapelModal'.$row->id.'" title="Tambah Guru Lain untuk Mapel ini"><i class="bi bi-person-plus"></i></button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editMapelModal'.$row->id.'" title="Edit"><i class="bi bi-pencil-square"></i></button>';
                    $btn .= csrf_field();
                    $btn .= method_field('DELETE');
                    $btn .= '<button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus mata pelajaran ini?\')" title="Hapus"><i class="bi bi-trash"></i></button>';
                    $btn .= '</form>';
                    
                    return $btn . $modal . $duplicateModal;
                })
                ->rawColumns(['guru_pengampu', 'kategori_badge', 'action'])
                ->make(true);
        }

        $gurus = Guru::orderBy('nama', 'asc')->get();
        return view('admin.mapels.index', compact('gurus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'kode_mapel' => [
                'required',
                Rule::unique('mapels')->where(function ($query) use ($request) {
                    return $query->where('guru_id', $request->guru_id);
                })
            ],
            'nama_mapel' => 'required',
            'kategori' => 'nullable',
            'guru_id' => 'nullable|exists:gurus,id'
        ]);

        Mapel::create($request->all());

        return redirect()->route('mapels.index')
                        ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'kode_mapel' => [
                'required',
                Rule::unique('mapels')->where(function ($query) use ($request) {
                    return $query->where('guru_id', $request->guru_id);
                })->ignore($id)
            ],
            'nama_mapel' => 'required',
            'kategori' => 'nullable',
            'guru_id' => 'nullable|exists:gurus,id'
        ]);

        $mapel = Mapel::findOrFail($id);
        $mapel->update($request->all());

        return redirect()->route('mapels.index')
                        ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Mapel::findOrFail($id)->delete();
        return redirect()->route('mapels.index')
                        ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
