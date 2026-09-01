<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SiswaProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->guru;
        $mapels = $guru->mapels()->with('kelas')->get();
        return view('guru.progress.index', compact('mapels'));
    }

    public function show($mapel_id, $kelas_id)
    {
        $guru = Auth::user()->guru;
        $mapel = Mapel::findOrFail($mapel_id);
        $kelas = Kelas::findOrFail($kelas_id);

        if ($mapel->guru_id != $guru->id) {
            abort(403);
        }

        $materis = $mapel->materis()->orderBy('urutan', 'asc')->get();
        $siswas = Siswa::where('kelas_id', $kelas_id)->get();
        $tugases = $mapel->tugas()->orderBy('created_at', 'asc')->get();

        return view('guru.progress.show', compact('mapel', 'kelas', 'materis', 'siswas', 'tugases'));
    }

    public function data($mapel_id, $kelas_id)
    {
        $guru = Auth::user()->guru;
        $mapel = Mapel::findOrFail($mapel_id);
        
        if ($mapel->guru_id != $guru->id) abort(403);

        $materis = $mapel->materis()->withCount('pretest_questions')->orderBy('urutan', 'asc')->get();
        $tugases = $mapel->tugas()->orderBy('created_at', 'asc')->get();

        $query = Siswa::where('kelas_id', $kelas_id)->with(['progress', 'tugas_submissions']);

        $datatables = \Yajra\DataTables\Facades\DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('siswa_info', function ($siswa) {
                return '<div class="fw-bold text-dark">'.$siswa->nama.'</div><div class="small text-muted">'.$siswa->nis.'</div>';
            });

        $rawColumns = ['siswa_info'];

        foreach ($materis as $materi) {
            $colName = 'materi_' . $materi->id;
            $datatables->addColumn($colName, function ($siswa) use ($materi) {
                $progress = $siswa->progress->where('materi_id', $materi->id)->first();
                $hasPretest = $materi->pretest_questions_count > 0;
                
                if ($progress && $progress->is_completed) {
                    if ($hasPretest) {
                        $color = $progress->pretest_nilai >= 75 ? 'text-success' : 'text-danger';
                        $html = '<div class="fw-bold fs-5 ' . $color . '">' . $progress->pretest_nilai . '</div>';
                    } else {
                        $html = '<div class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-person-check-fill me-1"></i> Hadir & Selesai</div>';
                    }
                    
                    if ($progress->cerita_reflektif) {
                        $html .= '<div class="mt-2"><button type="button" class="btn btn-sm btn-outline-primary rounded-pill border-0 d-print-none" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#refleksiModal'.$siswa->id.$materi->id.'"><i class="bi bi-chat-left-text"></i> Baca Refleksi</button></div>';
                        
                        $html .= '
                        <div class="modal fade text-start" id="refleksiModal'.$siswa->id.$materi->id.'" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom-0 p-4 pb-0">
                                        <h5 class="modal-title fw-bold">Cerita Reflektif</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">' . substr($siswa->nama, 0, 1) . '</div>
                                            <div>
                                                <div class="fw-bold text-dark">' . $siswa->nama . '</div>
                                                <div class="small text-muted">Modul ' . $materi->urutan . ': ' . $materi->judul . '</div>
                                            </div>
                                        </div>
                                        <div class="bg-light p-4 rounded-4 text-dark" style="font-size: 0.95rem; line-height: 1.6;">"' . htmlspecialchars($progress->cerita_reflektif) . '"</div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                    return $html;
                } else {
                    if ($progress && ($progress->pdf_dibaca || $progress->video_ditonton || !empty($progress->cerita_reflektif))) {
                        return '<div class="badge bg-info text-dark rounded-pill px-3 py-2"><i class="bi bi-person-check me-1"></i> Hadir (Proses)</div>';
                    } else {
                        return '<div class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1" style="font-size: 0.7rem;"><i class="bi bi-x-circle me-1"></i> Belum Hadir</div>';
                    }
                }
            });
            $rawColumns[] = $colName;
        }

        foreach ($tugases as $tugas) {
            $colName = 'tugas_' . $tugas->id;
            $datatables->addColumn($colName, function ($siswa) use ($tugas) {
                $submission = $siswa->tugas_submissions->where('tugas_id', $tugas->id)->first();
                if ($submission) {
                    if ($submission->nilai !== null) {
                        $color = $submission->nilai >= 75 ? 'text-success' : 'text-danger';
                        return '<div class="fw-bold fs-5 ' . $color . '">' . $submission->nilai . '</div>';
                    } else {
                        return '<div class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.75rem;"><i class="bi bi-hourglass-split me-1"></i> Perlu Dinilai</div>';
                    }
                } else {
                    if (\Carbon\Carbon::parse($tugas->tenggat_waktu)->lt(now())) {
                        return '<div class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 0.75rem;"><i class="bi bi-x-circle me-1"></i> Tidak Kumpul</div>';
                    } else {
                        return '<div class="badge bg-light text-muted border rounded-pill px-2 py-1" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> Menunggu</div>';
                    }
                }
            });
            $rawColumns[] = $colName;
        }

        $datatables->addColumn('rata_rata', function ($siswa) use ($materis, $tugases) {
            $totalNilai = 0;
            $countPretest = 0;
            $countSelesai = 0;

            foreach ($materis as $materi) {
                $progress = $siswa->progress->where('materi_id', $materi->id)->first();
                $hasPretest = $materi->pretest_questions_count > 0;
                if ($progress && $progress->is_completed) {
                    if ($hasPretest && $progress->pretest_nilai !== null) {
                        $totalNilai += $progress->pretest_nilai;
                        $countPretest++;
                    }
                    $countSelesai++;
                }
            }

            foreach ($tugases as $tugas) {
                $submission = $siswa->tugas_submissions->where('tugas_id', $tugas->id)->first();
                if ($submission && $submission->nilai !== null) {
                    $totalNilai += $submission->nilai;
                    $countPretest++;
                }
            }

            if ($countPretest > 0) {
                $rata = round($totalNilai / $countPretest);
                $color = $rata >= 75 ? 'text-success' : 'text-danger';
                $html = '<h5 class="fw-bold mb-0 ' . $color . '">' . $rata . '</h5>';
            } else {
                $html = '<span class="text-muted small">-</span>';
            }
            $html .= '<div class="small text-muted mt-1" style="font-size: 0.7rem;">Materi: ' . $countSelesai . '/' . $materis->count() . '</div>';
            return $html;
        });
        $rawColumns[] = 'rata_rata';

        return $datatables->rawColumns($rawColumns)->make(true);
    }

    public function export($mapel_id, $kelas_id)
    {
        $guru = Auth::user()->guru;
        $mapel = Mapel::findOrFail($mapel_id);
        $kelas = Kelas::findOrFail($kelas_id);

        if ($mapel->guru_id != $guru->id) {
            abort(403);
        }

        $materis = $mapel->materis()->orderBy('urutan', 'asc')->get();
        $siswas = Siswa::where('kelas_id', $kelas_id)->get();
        
        $siswaIds = $siswas->pluck('id');
        $materiIds = $materis->pluck('id');
        
        $progressRecords = SiswaProgress::whereIn('siswa_id', $siswaIds)
                                        ->whereIn('materi_id', $materiIds)
                                        ->get();

        $progressMatrix = [];
        foreach ($progressRecords as $record) {
            $progressMatrix[$record->siswa_id][$record->materi_id] = $record;
        }

        $tugases = $mapel->tugas()->orderBy('created_at', 'asc')->get();
        $tugasIds = $tugases->pluck('id');
        $tugasSubmissions = \App\Models\TugasSubmission::whereIn('siswa_id', $siswaIds)
                                                       ->whereIn('tugas_id', $tugasIds)
                                                       ->get();
        
        $tugasMatrix = [];
        foreach ($tugasSubmissions as $submission) {
            $tugasMatrix[$submission->siswa_id][$submission->tugas_id] = $submission;
        }

        $filename = 'Kehadiran_Nilai_' . str_replace(' ', '_', $mapel->nama_mapel) . '_' . str_replace(' ', '_', $kelas->nama_kelas) . '_' . date('Ymd_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\GuruProgressExport($mapel, $kelas, $materis, $siswas, $progressMatrix, $tugases, $tugasMatrix),
            $filename
        );
    }
}
