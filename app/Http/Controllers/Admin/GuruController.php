<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Guru::with('user')->latest();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="form-check-input item-checkbox">';
                })
                ->addColumn('nama_guru', function ($row) {
                    if ($row->user && $row->user->avatar) {
                        $avatarUrl = asset('storage/' . $row->user->avatar);
                        $avatarHtml = '<img src="' . $avatarUrl . '" alt="Avatar" class="rounded-circle object-fit-cover me-3 shadow-sm border" style="width: 40px; height: 40px;">';
                    } else {
                        $initial = substr($row->nama, 0, 1);
                        $avatarHtml = '<div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                           <span class="fw-bold text-success">' . $initial . '</span>
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
                ->addColumn('status_akun', function ($row) {
                    if ($row->user_id) {
                        return '<span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle me-1"></i> Terhubung</span>';
                    } else {
                        return '<form action="' . route('gurus.generate-account', $row->id) . '" method="POST" class="d-inline">
                                    ' . csrf_field() . '
                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="return confirm(\'Sistem akan membuatkan akun untuk guru ini. Lanjutkan?\')">
                                        <i class="bi bi-magic me-1"></i> Generate Akun
                                    </button>
                                </form>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $modal = '<div class="modal fade text-start" id="editGuruModal' . $row->id . '" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                  <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                                    <h5 class="modal-title fw-bold">Edit Data Guru</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <form method="POST" action="' . route('gurus.update', $row->id) . '">
                                      ' . csrf_field() . '
                                      ' . method_field('PUT') . '
                                      <div class="modal-body p-4">
                                          <div class="mb-3">
                                              <label class="form-label fw-semibold text-muted">Nomor Induk Pegawai (NIP)</label>
                                              <input type="text" name="nip" class="form-control form-control-lg rounded-3" value="' . $row->nip . '" required>
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
                                              <label class="form-label fw-semibold text-muted">Nomor Telepon</label>
                                              <input type="text" name="telepon" class="form-control form-control-lg rounded-3" value="' . $row->telepon . '">
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
                        $btn .= '<form action="' . route('gurus.reset-password', $row->id) . '" method="POST" class="d-inline">';
                        $btn .= csrf_field();
                        $btn .= '<button type="submit" class="btn btn-sm btn-light border text-warning" onclick="return confirm(\'Apakah Anda yakin ingin mereset password ke default (password)?\')"><i class="bi bi-key"></i> Reset</button>';
                        $btn .= '</form>';
                    }
                    $btn .= '<button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editGuruModal' . $row->id . '"><i class="bi bi-pencil-square"></i> Edit</button>';
                    $btn .= '<form action="' . route('gurus.destroy', $row->id) . '" method="POST" class="d-inline">';
                    $btn .= csrf_field();
                    $btn .= method_field('DELETE');
                    $btn .= '<button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data guru ini?\')"><i class="bi bi-trash"></i> Hapus</button>';
                    $btn .= '</form>';
                    $btn .= '</div>';

                    return $btn . $modal;
                })
                ->rawColumns(['checkbox', 'nama_guru', 'status_akun', 'action'])
                ->make(true);
        }

        return view('admin.gurus.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nip' => 'required|unique:gurus,nip',
            'nama' => 'required',
            'jenis_kelamin' => 'required',
        ]);

        Guru::create($request->all());

        return redirect()->route('gurus.index')
            ->with('success', 'Data Guru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'nip' => 'required|unique:gurus,nip,' . $id,
            'nama' => 'required',
            'jenis_kelamin' => 'required',
        ]);

        $guru = Guru::findOrFail($id);
        $guru->update($request->all());

        return redirect()->route('gurus.index')
            ->with('success', 'Data Guru berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $guru = Guru::findOrFail($id);

        // Delete associated user if exists
        if ($guru->user_id) {
            User::find($guru->user_id)->delete();
        }

        $guru->delete();

        return redirect()->route('gurus.index')
            ->with('success', 'Data Guru berhasil dihapus.');
    }

    /**
     * Reset user password to default.
     */
    public function resetPassword(string $id)
    {
        $guru = Guru::findOrFail($id);
        if ($guru->user_id) {
            $user = User::find($guru->user_id);
            if ($user) {
                $user->password = Hash::make('password');
                $user->save();
                return redirect()->route('gurus.index')->with('success', 'Password akun guru berhasil direset ke default (password).');
            }
        }
        return redirect()->route('gurus.index')->with('error', 'Guru belum memiliki akun.');
    }

    /**
     * Remove the multiple specified resources from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if ($ids) {
            $gurus = Guru::whereIn('id', $ids)->get();
            foreach ($gurus as $guru) {
                // Delete associated user if exists
                if ($guru->user_id) {
                    User::find($guru->user_id)->delete();
                }
                $guru->delete();
            }
            return response()->json(['success' => 'Data guru terpilih berhasil dihapus.']);
        }
        return response()->json(['error' => 'Tidak ada data yang dipilih.'], 400);
    }

    /**
     * Generate user account for guru
     */
    public function generateAccount(string $id)
    {
        $guru = Guru::findOrFail($id);

        if ($guru->user_id) {
            return redirect()->route('gurus.index')->with('error', 'Guru ini sudah memiliki akun.');
        }

        $identifier = $guru->nip ? str_replace(' ', '', $guru->nip) : strtolower(str_replace(' ', '', $guru->nama));
        $email = $identifier . '@lms.com';
        $password = 'password';

        // Check if email already exists
        $originalEmail = $email;
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = str_replace('@lms.com', $counter . '@lms.com', $originalEmail);
            $counter++;
        }

        $user = User::create([
            'name' => $guru->nama,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $user->assignRole('guru');

        $guru->user_id = $user->id;
        $guru->save();

        return redirect()->route('gurus.index')->with('success', 'Akun berhasil dibuat! Email: ' . $email . ' | Password: ' . $password);
    }

    /**
     * Generate user account for all guru
     */
    public function generateAllAccount()
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $gurus = Guru::whereNull('user_id')->get();
        $count = 0;

        foreach ($gurus as $guru) {
            $identifier = $guru->nip ? str_replace(' ', '', $guru->nip) : strtolower(str_replace(' ', '', $guru->nama));
            $email = $identifier . '@lms.com';

            // Generate unique email if it exists
            $originalEmail = $email;
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = str_replace('@lms.com', $counter . '@lms.com', $originalEmail);
                $counter++;
            }

            $user = User::create([
                'name' => $guru->nama,
                'email' => $email,
                'password' => Hash::make('password'),
            ]);

            $user->assignRole('guru');

            $guru->user_id = $user->id;
            $guru->save();
            $count++;
        }

        if ($count > 0) {
            return redirect()->route('gurus.index')->with('success', $count . ' Akun Guru berhasil di-generate secara massal! Password default: password');
        } else {
            return redirect()->route('gurus.index')->with('info', 'Semua Guru sudah memiliki akun.');
        }
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\GuruTemplateExport, 'Template_Import_Guru.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\GuruImport, $request->file('file_excel'));
            return redirect()->route('gurus.index')->with('success', 'Data Guru berhasil diimpor dari Excel!');
        } catch (\Exception $e) {
            return redirect()->route('gurus.index')->with('error', 'Terjadi kesalahan saat impor: ' . $e->getMessage());
        }
    }
}
