<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('roles')->latest();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function($row){
                    return '<input type="checkbox" name="ids[]" value="'.$row->id.'" class="form-check-input item-checkbox">';
                })
                ->addColumn('user_name', function($row){
                    $initial = substr($row->name, 0, 1);
                    return '<div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <span class="fw-bold text-secondary">'.$initial.'</span>
                                </div>
                                '.$row->name.'
                            </div>';
                })
                ->addColumn('roles_badge', function($row){
                    $badges = '';
                    if(!empty($row->getRoleNames())){
                        foreach($row->getRoleNames() as $v){
                            $color = 'bg-secondary';
                            if($v == 'admin') $color = 'bg-primary';
                            if($v == 'guru') $color = 'bg-success';
                            if($v == 'siswa') $color = 'bg-info';
                            $badges .= '<span class="badge '.$color.' rounded-pill px-3 me-1">'.ucfirst($v).'</span>';
                        }
                    }
                    return $badges;
                })
                ->addColumn('action', function($row){
                    $rolesList = '';
                    if(!empty($row->getRoleNames())){
                        foreach($row->getRoleNames() as $v){
                            $rolesList .= ucfirst($v) . ' ';
                        }
                    }
                    $date = $row->created_at ? $row->created_at->format('d M Y, H:i') : '-';
                    $initial = substr($row->name, 0, 1);
                    
                    $modal = '<div class="modal fade text-start" id="viewUserModal'.$row->id.'" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                  <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                                    <h5 class="modal-title fw-bold">Detail Pengguna</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body p-4">
                                      <div class="text-center mb-4">
                                          <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                              <span class="fw-bold text-secondary fs-1">'.$initial.'</span>
                                          </div>
                                          <h5 class="fw-bold mb-0">'.$row->name.'</h5>
                                          <p class="text-muted">'.$row->email.'</p>
                                      </div>
                                      
                                      <div class="card bg-light border-0 rounded-3">
                                          <div class="card-body">
                                              <div class="row mb-2">
                                                  <div class="col-5 text-muted fw-semibold">ID Pengguna</div>
                                                  <div class="col-7">: '.$row->id.'</div>
                                              </div>
                                              <div class="row mb-2">
                                                  <div class="col-5 text-muted fw-semibold">Jabatan (Role)</div>
                                                  <div class="col-7">: '.$rolesList.'</div>
                                              </div>
                                              <div class="row mb-2">
                                                  <div class="col-5 text-muted fw-semibold">Password</div>
                                                  <div class="col-7 text-danger">: <em><i class="bi bi-lock-fill"></i> Terenkripsi</em></div>
                                              </div>
                                              <div class="row">
                                                  <div class="col-5 text-muted fw-semibold">Tanggal Daftar</div>
                                                  <div class="col-7">: '.$date.'</div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="modal-footer border-top-0 pb-4 px-4">
                                      <button type="button" class="btn btn-light rounded-3 px-4 w-100" data-bs-dismiss="modal">Tutup</button>
                                  </div>
                                </div>
                              </div>
                            </div>';
                    
                    $btn = '<div class="d-flex justify-content-end gap-2">';
                    $btn .= '<form action="'.route('users.reset-password', $row->id).'" method="POST" class="d-inline">';
                    $btn .= csrf_field();
                    $btn .= '<button type="submit" class="btn btn-sm btn-light border text-warning" onclick="return confirm(\'Apakah Anda yakin ingin mereset password pengguna ini ke default?\')"><i class="bi bi-key"></i> Reset</button>';
                    $btn .= '</form>';
                    $btn .= '<button type="button" class="btn btn-sm btn-light border text-info" data-bs-toggle="modal" data-bs-target="#viewUserModal'.$row->id.'"><i class="bi bi-eye"></i> Detail</button>';
                    $btn .= '<a class="btn btn-sm btn-light border text-primary" href="'.route('users.edit', $row->id).'"><i class="bi bi-pencil-square"></i> Edit</a>';
                    $btn .= '<form action="'.route('users.destroy', $row->id).'" method="POST" class="d-inline">';
                    $btn .= csrf_field();
                    $btn .= method_field('DELETE');
                    $btn .= '<button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus user ini?\')"><i class="bi bi-trash"></i> Hapus</button>';
                    $btn .= '</form>';
                    $btn .= '</div>';
                    
                    return $btn . $modal;
                })
                ->rawColumns(['checkbox', 'user_name', 'roles_badge', 'action'])
                ->make(true);
        }

        $roles = Role::pluck('name', 'name')->all();
        return view('admin.users.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
                        ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
    
        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = \Illuminate\Support\Arr::except($input,array('password'));    
        }
    
        $user = User::find($id);
        $user->update($input);
        
        \DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Reset user password to default.
     */
    public function resetPassword(string $id)
    {
        $user = User::findOrFail($id);
        
        $password = 'password';
        if ($user->hasRole('siswa')) {
            $siswa = \App\Models\Siswa::where('user_id', $user->id)->first();
            if ($siswa) {
                $password = $siswa->nis;
            }
        }
        
        $user->password = Hash::make($password);
        $user->save();
        
        return redirect()->route('users.index')->with('success', 'Password pengguna berhasil direset ke default.');
    }

    /**
     * Remove the multiple specified resources from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if ($ids) {
            User::whereIn('id', $ids)->delete();
            return response()->json(['success' => 'Pengguna terpilih berhasil dihapus.']);
        }
        return response()->json(['error' => 'Tidak ada pengguna yang dipilih.'], 400);
    }

    /**
     * Download Excel daftar akun dan password guru
     */
    public function exportGuru()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AkunGuruExport, 'Daftar_Akun_Login_Guru_' . date('Y_m_d') . '.xlsx');
    }

    /**
     * Download Excel daftar akun dan password siswa
     */
    public function exportSiswa(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $namaKelas = 'Semua_Kelas';
        if ($kelasId) {
            $kelas = \App\Models\Kelas::find($kelasId);
            if ($kelas) {
                $namaKelas = 'Kelas_' . str_replace(' ', '_', $kelas->nama_kelas);
            }
        }
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AkunSiswaExport($kelasId), 'Daftar_Akun_Login_Siswa_' . $namaKelas . '_' . date('Y_m_d') . '.xlsx');
    }
}
