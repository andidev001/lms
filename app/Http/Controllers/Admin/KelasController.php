<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();
            $query = Kelas::with(['wali_kelas', 'mapels', 'siswas']);
            
            if ($activeTahunAjaran) {
                $query->where('tahun_ajaran_id', $activeTahunAjaran->id);
            }
            
            $data = $query->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('wali_kelas', function($row){
                    if ($row->wali_kelas) {
                        return '<div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <span class="fw-bold text-success" style="font-size: 0.8rem;">'.substr($row->wali_kelas->nama, 0, 1).'</span>
                                    </div>
                                    '.$row->wali_kelas->nama.'
                                </div>';
                    }
                    return '<span class="text-muted fst-italic">Belum ditentukan</span>';
                })
                ->addColumn('jumlah_mapel', function($row){
                    return '<span class="badge bg-primary rounded-pill px-3 py-2">'.$row->mapels->count().' Mapel</span>';
                })
                ->addColumn('jumlah_siswa', function($row){
                    $count = $row->siswas->count();
                    $color = $count > 0 ? 'btn-info text-white shadow-sm' : 'btn-light text-muted border';
                    
                    $badge = '<button type="button" class="btn btn-sm '.$color.' rounded-pill px-3 py-1 fw-semibold" data-bs-toggle="modal" data-bs-target="#viewSiswaKelasModal'.$row->id.'">
                                <i class="bi bi-people me-1"></i> '.$count.' Siswa
                              </button>';

                    $modal = '<div class="modal fade text-start" id="viewSiswaKelasModal'.$row->id.'" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                  <div class="modal-content border-0 shadow-lg rounded-4">
                                    <div class="modal-header border-bottom-0 pt-4 pb-2 px-4">
                                      <div>
                                        <h5 class="modal-title fw-bold mb-1"><i class="bi bi-people text-info me-2"></i> Daftar Siswa - '.$row->nama_kelas.'</h5>
                                        <p class="text-muted small mb-0">Total: <strong>'.$count.' Siswa</strong> terdaftar pada kelas ini.</p>
                                      </div>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">';
                    
                    if ($count > 0) {
                        $modal .= '<div class="table-responsive" style="max-height: 400px;">
                                     <table class="table table-hover align-middle mb-0">
                                       <thead class="table-light sticky-top">
                                         <tr>
                                           <th width="40px" class="py-2 text-muted fw-semibold">No</th>
                                           <th class="py-2 text-muted fw-semibold">NIS / NISN</th>
                                           <th class="py-2 text-muted fw-semibold">Nama Siswa</th>
                                           <th class="py-2 text-muted fw-semibold">Jenis Kelamin</th>
                                           <th class="py-2 text-muted fw-semibold text-end">Status Akun</th>
                                         </tr>
                                       </thead>
                                       <tbody>';
                        $no = 1;
                        foreach ($row->siswas->sortBy('nama') as $siswa) {
                            $jk = $siswa->jenis_kelamin == 'L' || strtolower($siswa->jenis_kelamin) == 'laki-laki' ? 'Laki-laki' : 'Perempuan';
                            $status = $siswa->user_id ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Aktif</span>' : '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3">Belum Ada Akun</span>';
                            
                            $modal .= '<tr>
                                         <td class="text-muted">'.$no++.'</td>
                                         <td class="fw-medium">'.$siswa->nis.'</td>
                                         <td class="fw-bold text-dark">'.$siswa->nama.'</td>
                                         <td>'.$jk.'</td>
                                         <td class="text-end">'.$status.'</td>
                                       </tr>';
                        }
                        $modal .= '    </tbody>
                                     </table>
                                   </div>';
                    } else {
                        $modal .= '<div class="alert alert-info border-0 bg-info bg-opacity-10 text-dark text-center py-4 mb-0 rounded-3">
                                     <i class="bi bi-info-circle fs-3 text-info d-block mb-2"></i>
                                     Belum ada siswa yang terdaftar di kelas <strong>'.$row->nama_kelas.'</strong>.<br>
                                     <small class="text-muted">Anda dapat menambahkan atau menginport data siswa di menu Kelola Siswa.</small>
                                   </div>';
                    }

                    $modal .= '      </div>
                                     <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                                       <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Tutup</button>
                                     </div>
                                   </div>
                                 </div>
                               </div>';
                    
                    return $badge . $modal;
                })
                ->addColumn('action', function($row){
                    $gurus = Guru::orderBy('nama', 'asc')->get();
                    $mapels = \App\Models\Mapel::with('guru_pengampu')->orderBy('nama_mapel', 'asc')->get();
                    
                    $guruOptions = '<option value="">-- Pilih Wali Kelas --</option>';
                    foreach ($gurus as $guru) {
                        $selected = $row->guru_id == $guru->id ? 'selected' : '';
                        $guruOptions .= '<option value="'.$guru->id.'" '.$selected.'>'.$guru->nama.'</option>';
                    }
                    
                    $kelasMapelIds = $row->mapels->pluck('id')->toArray();
                    $mapelOptions = '';
                    foreach ($mapels as $mapel) {
                        $selected = in_array($mapel->id, $kelasMapelIds) ? 'selected' : '';
                        $guruName = $mapel->guru_pengampu ? ' (' . $mapel->guru_pengampu->nama . ')' : ' (Tanpa Guru)';
                        $mapelOptions .= '<option value="'.$mapel->id.'" '.$selected.'>'.$mapel->nama_mapel.$guruName.'</option>';
                    }

                    $modal = '<div class="modal fade text-start" id="editKelasModal'.$row->id.'" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                  <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                                    <h5 class="modal-title fw-bold">Edit Data Kelas</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <form method="POST" action="'.route('kelas.update', $row->id).'">
                                      '.csrf_field().'
                                      '.method_field('PUT').'
                                      <div class="modal-body p-4">
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Kode Kelas</label>
                                              <input type="text" name="kode_kelas" class="form-control form-control-lg rounded-3" value="'.$row->kode_kelas.'" required>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Nama Kelas</label>
                                              <input type="text" name="nama_kelas" class="form-control form-control-lg rounded-3" value="'.$row->nama_kelas.'" required>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Wali Kelas (Opsional)</label>
                                              <select name="guru_id" class="form-select form-select-lg rounded-3">
                                                  '.$guruOptions.'
                                              </select>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Mata Pelajaran (Opsional)</label>
                                              <select name="mapel_ids[]" class="form-select form-select-lg rounded-3" multiple style="height: 120px;">
                                                  '.$mapelOptions.'
                                              </select>
                                              <div class="form-text">Tahan tombol Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari satu.</div>
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
                            
                    $btn = '<form action="'.route('kelas.destroy', $row->id).'" method="POST" class="d-flex justify-content-end gap-2">';
                    $btn .= '<button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editKelasModal'.$row->id.'"><i class="bi bi-pencil-square"></i> Edit</button>';
                    $btn .= csrf_field();
                    $btn .= method_field('DELETE');
                    $btn .= '<button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus kelas ini?\')"><i class="bi bi-trash"></i> Hapus</button>';
                    $btn .= '</form>';
                    
                    return $btn . $modal;
                })
                ->rawColumns(['wali_kelas', 'jumlah_mapel', 'jumlah_siswa', 'action'])
                ->make(true);
        }

        $gurus = Guru::orderBy('nama', 'asc')->get();
        return view('admin.kelas.index', compact('gurus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();

        $rules = [
            'kode_kelas' => 'required|unique:kelas,kode_kelas',
            'nama_kelas' => 'required',
            'guru_id' => 'nullable|exists:gurus,id|unique:kelas,guru_id',
            'mapel_ids' => 'nullable|array',
            'mapel_ids.*' => 'exists:mapels,id'
        ];

        if ($activeTahunAjaran) {
            $rules['guru_id'] = [
                'nullable',
                'exists:gurus,id',
                \Illuminate\Validation\Rule::unique('kelas', 'guru_id')->where('tahun_ajaran_id', $activeTahunAjaran->id)
            ];
        }

        $this->validate($request, $rules, [
            'guru_id.unique' => 'Nama guru sudah ada sebagai wali kelas di kelas lain.',
            'kode_kelas.unique' => 'Kode kelas sudah digunakan.'
        ]);

        $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();
        
        $data = $request->except('mapel_ids');
        if ($activeTahunAjaran) {
            $data['tahun_ajaran_id'] = $activeTahunAjaran->id;
        }

        $kelas = Kelas::create($data);
        
        if ($request->has('mapel_ids')) {
            $kelas->mapels()->sync($request->mapel_ids);
        }

        return redirect()->route('kelas.index')
                        ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();

        $rules = [
            'kode_kelas' => 'required|unique:kelas,kode_kelas,'.$id,
            'nama_kelas' => 'required',
            'guru_id' => 'nullable|exists:gurus,id|unique:kelas,guru_id,'.$id,
            'mapel_ids' => 'nullable|array',
            'mapel_ids.*' => 'exists:mapels,id'
        ];

        if ($activeTahunAjaran) {
            $rules['guru_id'] = [
                'nullable',
                'exists:gurus,id',
                \Illuminate\Validation\Rule::unique('kelas', 'guru_id')->where('tahun_ajaran_id', $activeTahunAjaran->id)->ignore($id)
            ];
        }

        $this->validate($request, $rules, [
            'guru_id.unique' => 'Nama guru sudah ada sebagai wali kelas di kelas lain.',
            'kode_kelas.unique' => 'Kode kelas sudah digunakan.'
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->except('mapel_ids'));
        
        if ($request->has('mapel_ids')) {
            $kelas->mapels()->sync($request->mapel_ids);
        } else {
            $kelas->mapels()->sync([]);
        }

        return redirect()->route('kelas.index')
                        ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Kelas::findOrFail($id)->delete();
        return redirect()->route('kelas.index')
                        ->with('success', 'Data kelas berhasil dihapus.');
    }
}
