<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Siswa::with(['user', 'kelas'])->latest();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="form-check-input item-checkbox">';
                })
                ->addColumn('nama_siswa', function ($row) {
                    if ($row->user && $row->user->avatar) {
                        $avatarUrl = asset('storage/' . $row->user->avatar);
                        $avatarHtml = '<img src="' . $avatarUrl . '" alt="Avatar" class="rounded-circle object-fit-cover me-3 shadow-sm border" style="width: 40px; height: 40px;">';
                    } else {
                        $initial = substr($row->nama, 0, 1);
                        $avatarHtml = '<div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                           <span class="fw-bold text-info">' . $initial . '</span>
                                       </div>';
                    }
                    return '<div class="d-flex align-items-center">
                                ' . $avatarHtml . '
                                ' . $row->nama . '
                            </div>';
                })
                ->addColumn('jk', function ($row) {
                    return $row->jenis_kelamin == 'Laki-laki' ? 'L' : 'P';
                })
                ->addColumn('nama_kelas', function ($row) {
                    return $row->kelas ? '<span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">' . $row->kelas->nama_kelas . '</span>' : '<span class="text-muted fst-italic">Belum ada</span>';
                })
                ->addColumn('status_akun', function ($row) {
                    if ($row->user_id) {
                        return '<span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle me-1"></i> Terhubung</span>';
                    } else {
                        return '<form action="' . route('siswas.generate-account', $row->id) . '" method="POST" class="d-inline">
                                    ' . csrf_field() . '
                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="return confirm(\'Sistem akan membuatkan akun untuk siswa ini. Lanjutkan?\')">
                                        <i class="bi bi-magic me-1"></i> Generate Akun
                                    </button>
                                </form>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $kelases = \App\Models\Kelas::orderBy('nama_kelas', 'asc')->get();
                    $kelasOptions = '<option value="">-- Pilih Kelas --</option>';
                    foreach ($kelases as $kelas) {
                        $selected = $row->kelas_id == $kelas->id ? 'selected' : '';
                        $kelasOptions .= '<option value="' . $kelas->id . '" ' . $selected . '>' . $kelas->nama_kelas . '</option>';
                    }

                    $modal = '<div class="modal fade text-start" id="editSiswaModal' . $row->id . '" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                  <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                                    <h5 class="modal-title fw-bold">Edit Data Siswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <form method="POST" action="' . route('siswas.update', $row->id) . '">
                                      ' . csrf_field() . '
                                      ' . method_field('PUT') . '
                                      <div class="modal-body p-4">
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Nomor Induk Siswa (NIS)</label>
                                              <input type="text" name="nis" class="form-control form-control-lg rounded-3" value="' . $row->nis . '" required>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Nama Lengkap</label>
                                              <input type="text" name="nama" class="form-control form-control-lg rounded-3" value="' . $row->nama . '" required>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Jenis Kelamin</label>
                                              <select name="jenis_kelamin" class="form-select form-select-lg rounded-3" required>
                                                  <option value="Laki-laki" ' . ($row->jenis_kelamin == 'Laki-laki' ? 'selected' : '') . '>Laki-laki</option>
                                                  <option value="Perempuan" ' . ($row->jenis_kelamin == 'Perempuan' ? 'selected' : '') . '>Perempuan</option>
                                              </select>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Kelas</label>
                                              <select name="kelas_id" class="form-select form-select-lg rounded-3">
                                                  ' . $kelasOptions . '
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
                    if ($row->user_id) {
                        $btn .= '<form action="' . route('siswas.reset-password', $row->id) . '" method="POST" class="d-inline">';
                        $btn .= csrf_field();
                        $btn .= '<button type="submit" class="btn btn-sm btn-light border text-warning" onclick="return confirm(\'Apakah Anda yakin ingin mereset password ke default (NIS)?\')"><i class="bi bi-key"></i> Reset</button>';
                        $btn .= '</form>';
                    }
                    $btn .= '<button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editSiswaModal' . $row->id . '"><i class="bi bi-pencil-square"></i> Edit</button>';
                    $btn .= '<form action="' . route('siswas.destroy', $row->id) . '" method="POST" class="d-inline">';
                    $btn .= csrf_field();
                    $btn .= method_field('DELETE');
                    $btn .= '<button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data siswa ini?\')"><i class="bi bi-trash"></i> Hapus</button>';
                    $btn .= '</form>';
                    $btn .= '</div>';

                    return $btn . $modal;
                })
                ->rawColumns(['checkbox', 'nama_siswa', 'nama_kelas', 'status_akun', 'action'])
                ->make(true);
        }

        $kelases = \App\Models\Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.siswas.index', compact('kelases'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nis' => 'required|unique:siswas,nis',
            'nama' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id'
        ]);

        Siswa::create($request->all());

        return redirect()->route('siswas.index')
            ->with('success', 'Data Siswa berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'nis' => 'required|unique:siswas,nis,' . $id,
            'nama' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id'
        ]);

        $siswa = Siswa::findOrFail($id);
        $siswa->update($request->all());

        return redirect()->route('siswas.index')
            ->with('success', 'Data Siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $siswa = Siswa::findOrFail($id);

        // Delete associated user if exists
        if ($siswa->user_id) {
            User::find($siswa->user_id)->delete();
        }

        $siswa->delete();

        return redirect()->route('siswas.index')
            ->with('success', 'Data Siswa berhasil dihapus.');
    }

    /**
     * Reset user password to default.
     */
    public function resetPassword(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        if ($siswa->user_id) {
            $user = User::find($siswa->user_id);
            if ($user) {
                $user->password = Hash::make($siswa->nis);
                $user->save();
                return redirect()->route('siswas.index')->with('success', 'Password akun siswa berhasil direset ke default (NIS).');
            }
        }
        return redirect()->route('siswas.index')->with('error', 'Siswa belum memiliki akun.');
    }

    /**
     * Remove the multiple specified resources from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if ($ids) {
            $siswas = Siswa::whereIn('id', $ids)->get();
            foreach ($siswas as $siswa) {
                // Delete associated user if exists
                if ($siswa->user_id) {
                    User::find($siswa->user_id)->delete();
                }
                $siswa->delete();
            }
            return response()->json(['success' => 'Data siswa terpilih berhasil dihapus.']);
        }
        return response()->json(['error' => 'Tidak ada data yang dipilih.'], 400);
    }

    /**
     * Generate user account for siswa
     */
    public function generateAccount(string $id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->user_id) {
            return redirect()->route('siswas.index')->with('error', 'Siswa ini sudah memiliki akun.');
        }

        $email = str_replace(' ', '', $siswa->nis) . '@lms.com';
        $password = $siswa->nis;

        // Check if email already exists (including soft deleted models)
        $originalEmail = $email;
        $counter = 1;
        while (\Illuminate\Support\Facades\DB::table('users')->where('email', $email)->exists()) {
            $email = str_replace('@lms.com', $counter . '@lms.com', $originalEmail);
            $counter++;
        }

        $user = User::create([
            'name' => $siswa->nama,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);
        $user->assignRole('siswa');

        $siswa->user_id = $user->id;
        $siswa->save();

        return redirect()->route('siswas.index')->with('success', 'Akun berhasil dibuat! Email: ' . $email . ' | Password: ' . $password);
    }

    /**
     * Generate user account for all siswa
     */
    public function generateAllAccount()
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);
        $siswas = Siswa::whereNull('user_id')->get();
        $count = 0;

        foreach ($siswas as $siswa) {
            $email = str_replace(' ', '', $siswa->nis) . '@lms.com';

            // Generate unique email if it exists (including soft deleted models)
            $originalEmail = $email;
            $counter = 1;
            while (\Illuminate\Support\Facades\DB::table('users')->where('email', $email)->exists()) {
                $email = str_replace('@lms.com', $counter . '@lms.com', $originalEmail);
                $counter++;
            }

            $user = User::create([
                'name' => $siswa->nama,
                'email' => $email,
                'password' => Hash::make($siswa->nis),
            ]);

            $user->assignRole('siswa');

            $siswa->user_id = $user->id;
            $siswa->save();
            $count++;
        }

        if ($count > 0) {
            return redirect()->route('siswas.index')->with('success', $count . ' Akun Siswa berhasil di-generate secara massal! Password default: NIS masing-masing');
        } else {
            return redirect()->route('siswas.index')->with('info', 'Semua Siswa sudah memiliki akun.');
        }
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SiswaTemplateExport, 'Template_Import_Siswa.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SiswaImport, $request->file('file_excel'));
            return redirect()->route('siswas.index')->with('success', 'Data Siswa berhasil diimpor dari Excel!');
        } catch (\Exception $e) {
            return redirect()->route('siswas.index')->with('error', 'Terjadi kesalahan saat impor: ' . $e->getMessage());
        }
    }
}
