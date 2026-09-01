<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasPenilaianController extends Controller
{
    public function index(Request $request, $mapel_id, $tugas_id)
    {
        $tugas = Tugas::with(['mapel'])->findOrFail($tugas_id);
        
        if ($tugas->mapel->guru_id != Auth::user()->guru->id) abort(403);
        
        $kelasList = \App\Models\Kelas::whereHas('mapels', function($q) use ($tugas) {
            $q->where('mapels.id', $tugas->mapel_id);
        })->get();

        $kelas_id = $request->input('kelas_id');

        return view('guru.tugas.penilaian', compact('tugas', 'kelasList', 'kelas_id'));
    }

    public function data(Request $request, $mapel_id, $tugas_id)
    {
        $tugas = Tugas::findOrFail($tugas_id);
        
        if ($tugas->mapel->guru_id != Auth::user()->guru->id) abort(403);
        
        $kelas_id = $request->input('kelas_id');

        $query = \App\Models\Siswa::whereHas('kelas', function($query) use ($tugas) {
            $query->whereHas('mapels', function($q) use ($tugas) {
                $q->where('mapels.id', $tugas->mapel_id);
            });
        })->with(['kelas', 'tugas_submissions' => function($q) use ($tugas_id) {
            $q->where('tugas_id', $tugas_id);
        }]);

        if ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        }

        return \Yajra\DataTables\Facades\DataTables::of($query)
            ->addColumn('siswa_info', function ($siswa) {
                return '<div class="fw-bold text-dark">'.$siswa->nama.'</div><div class="small text-muted">NIS: '.$siswa->nis.'</div>';
            })
            ->addColumn('kelas_nama', function ($siswa) {
                return '<span class="badge bg-light text-dark border">'.($siswa->kelas->nama_kelas ?? '-').'</span>';
            })
            ->addColumn('status_waktu', function ($siswa) use ($tugas) {
                $submission = $siswa->tugas_submissions->first();
                if ($submission) {
                    $html = '<div class="d-flex flex-column">';
                    if ($submission->waktu_pengumpulan > $tugas->tenggat_waktu) {
                        $html .= '<span class="badge bg-danger mb-1" style="width: fit-content;">Terlambat</span>';
                    } else {
                        $html .= '<span class="badge bg-success mb-1" style="width: fit-content;">Tepat Waktu</span>';
                    }
                    $html .= '<small class="text-muted"><i class="bi bi-clock-history me-1"></i>'.\Carbon\Carbon::parse($submission->waktu_pengumpulan)->format('d M, H:i').'</small></div>';
                    return $html;
                }
                return '<span class="badge bg-secondary bg-opacity-10 text-secondary border">Belum Mengumpulkan</span>';
            })
            ->addColumn('jawaban', function ($siswa) {
                $submission = $siswa->tugas_submissions->first();
                if ($submission) {
                    $html = '<div class="d-flex flex-wrap gap-2">';
                    if ($submission->teks_jawaban) {
                        $html .= '<button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalTeks'.$siswa->id.'"><i class="bi bi-justify-left me-1"></i> Teks</button>';
                        
                        $html .= '
                        <div class="modal fade text-start" id="modalTeks'.$siswa->id.'" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header border-bottom-0">
                                        <h5 class="modal-title fw-bold">Jawaban: '.$siswa->nama.'</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="p-3 bg-light rounded-3 border">
                                            '.nl2br(e($submission->teks_jawaban)).'
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                    if ($submission->file_jawaban) {
                        $url = \Illuminate\Support\Facades\Storage::url($submission->file_jawaban);
                        $html .= '<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFile'.$siswa->id.'"><i class="bi bi-eye me-1"></i> File</button>';
                        $html .= '
                        <div class="modal fade text-start" id="modalFile'.$siswa->id.'" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header border-bottom-0">
                                        <h5 class="modal-title fw-bold">File Jawaban: '.$siswa->nama.'</h5>
                                        <a href="'.$url.'" target="_blank" class="btn btn-sm btn-primary ms-3"><i class="bi bi-download"></i> Download</a>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-0 bg-light" style="height: 80vh;">
                                        <iframe src="'.$url.'" style="width: 100%; height: 100%; border: none;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                    if ($submission->link_jawaban) {
                        $html .= '<a href="'.$submission->link_jawaban.'" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-link-45deg me-1"></i> Link</a>';
                    }
                    $html .= '</div>';
                    return $html;
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('nilai', function ($siswa) {
                $submission = $siswa->tugas_submissions->first();
                if ($submission) {
                    if ($submission->nilai !== null) {
                        return '<span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">'.$submission->nilai.'</span>';
                    }
                    return '<span class="badge bg-warning text-dark">Belum Dinilai</span>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('aksi', function ($siswa) use ($tugas) {
                $submission = $siswa->tugas_submissions->first();
                if ($submission) {
                    $route = route('guru.mapels.tugas.penilaian.store', [$tugas->mapel_id, $tugas->id, $submission->id]);
                    $catatan = $submission->catatan_guru ? htmlspecialchars($submission->catatan_guru) : '';
                    $html = '<button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNilai'.$siswa->id.'">Nilai</button>';
                    
                    $html .= '
                    <div class="modal fade text-start" id="modalNilai'.$siswa->id.'" aria-hidden="true" data-bs-focus="false">
                        <div class="modal-dialog modal-dialog-centered text-start">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Beri Nilai: '.$siswa->nama.'</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="'.$route.'" method="POST">
                                    '.csrf_field().'
                                    <div class="modal-body py-4">
                                        <div class="mb-4 text-center">
                                            <label class="form-label fw-bold text-muted mb-3 d-block">Skor Nilai (0 - 100)</label>
                                            <input type="number" name="nilai" class="form-control form-control-lg text-center mx-auto fw-bold text-primary" style="font-size: 2rem; width: 150px;" value="'.$submission->nilai.'" min="0" max="100" required>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold text-muted">Catatan Tambahan / Feedback (Opsional)</label>
                                            <textarea name="catatan_guru" class="form-control rounded-3 rich-text" rows="3" placeholder="Misal: Kerja bagus, tapi bagian A kurang lengkap...">'.$catatan.'</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Nilai</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>';
                    return $html;
                }
                return '';
            })
            ->rawColumns(['siswa_info', 'kelas_nama', 'status_waktu', 'jawaban', 'nilai', 'aksi'])
            ->make(true);
    }

    public function export(Request $request, $mapel_id, $tugas_id)
    {
        $tugas = Tugas::findOrFail($tugas_id);
        if ($tugas->mapel->guru_id != Auth::user()->guru->id) abort(403);
        
        $kelas_id = $request->input('kelas_id');
        $nama_file = 'Nilai_Tugas_' . str_replace(' ', '_', $tugas->judul) . '_' . date('YmdHis') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\NilaiTugasExport($tugas_id, $kelas_id), $nama_file);
    }

    public function store(Request $request, $mapel_id, $tugas_id, $submission_id)
    {
        $tugas = Tugas::findOrFail($tugas_id);
        if ($tugas->mapel->guru_id != Auth::user()->guru->id) abort(403);

        $submission = TugasSubmission::where('id', $submission_id)->where('tugas_id', $tugas_id)->firstOrFail();

        $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'catatan_guru' => 'nullable|string'
        ]);

        $submission->update([
            'nilai' => $request->nilai,
            'catatan_guru' => $request->catatan_guru
        ]);

        return redirect()->back()->with('success', 'Nilai berhasil disimpan!');
    }
}
