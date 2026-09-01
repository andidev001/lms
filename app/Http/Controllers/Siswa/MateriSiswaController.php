<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\SiswaProgress;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MateriSiswaController extends Controller
{
    /**
     * Tampilkan daftar mata pelajaran siswa (Dashboard)
     */
    public function index()
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa || !$siswa->kelas) {
            return view('siswa.belajar.index')->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        if ($activeTahunAjaran && $siswa->kelas->tahun_ajaran_id && $siswa->kelas->tahun_ajaran_id != $activeTahunAjaran->id) {
            // Optional: bisa menambahkan warning flash message di sini jika dibutuhkan
            // session()->flash('warning', 'Kelas Anda bukan bagian dari Tahun Ajaran yang aktif saat ini.');
        }

        $mapels = $siswa->kelas->mapels;
        return view('siswa.belajar.index', compact('mapels'));
    }

    /**
     * Tampilkan timeline materi dari sebuah Mapel
     */
    public function showMapel(Request $request, $mapel_id)
    {
        $siswa = Auth::user()->siswa;
        $mapel = Mapel::findOrFail($mapel_id);

        // Pastikan mapel ini ada di kelas siswa
        if (!$siswa->kelas->mapels->contains($mapel_id)) {
            abort(403);
        }

        $materis = Materi::where('mapel_id', $mapel_id)->orderBy('urutan', 'asc')->get();
        
        // Ambil progress siswa
        $progress = SiswaProgress::where('siswa_id', $siswa->id)
                                 ->whereIn('materi_id', $materis->pluck('id'))
                                 ->get()
                                 ->keyBy('materi_id');

        // Tentukan materi aktif di kolom kanan
        $activeMateri = null;
        if ($request->has('materi_id')) {
            $activeMateri = $materis->where('id', $request->materi_id)->first();
        } 
        
        // Jika tidak ada parameter materi_id atau invalid, pilih materi pertama yang belum selesai
        if (!$activeMateri) {
            foreach($materis as $m) {
                if(!isset($progress[$m->id]) || !$progress[$m->id]->is_completed) {
                    $activeMateri = $m;
                    break;
                }
            }
            // Jika semuanya selesai, pilih materi terakhir
            if(!$activeMateri && $materis->count() > 0) {
                $activeMateri = $materis->last();
            }
        }

        return view('siswa.belajar.mapel', compact('mapel', 'materis', 'progress', 'activeMateri'));
    }

    /**
     * Tampilkan halaman detail materi (PDF/Video)
     */
    public function showMateri($mapel_id, $materi_id)
    {
        $siswa = Auth::user()->siswa;
        $mapel = Mapel::findOrFail($mapel_id);
        $materi = Materi::where('mapel_id', $mapel_id)->findOrFail($materi_id);

        // Sequential validation: Cek apakah materi sebelumnya sudah selesai
        $previousMateri = Materi::where('mapel_id', $mapel_id)
                                ->where('urutan', '<', $materi->urutan)
                                ->orderBy('urutan', 'desc')
                                ->first();

        if ($previousMateri) {
            $prevProgress = SiswaProgress::where('siswa_id', $siswa->id)
                                         ->where('materi_id', $previousMateri->id)
                                         ->first();
            
            if (!$prevProgress || !$prevProgress->is_completed) {
                return redirect()->route('siswa.mapels.show', $mapel_id)
                                 ->with('error', 'Anda harus menyelesaikan materi sebelumnya terlebih dahulu.');
            }
        }

        // Ambil atau buat progress baru
        $progress = SiswaProgress::firstOrCreate([
            'siswa_id' => $siswa->id,
            'materi_id' => $materi->id
        ]);

        return view('siswa.belajar.materi', compact('mapel', 'materi', 'progress'));
    }

    /**
     * Tandai PDF/Video selesai (dipanggil via AJAX atau form)
     */
    public function markProgress(Request $request, $mapel_id, $materi_id)
    {
        $siswa = Auth::user()->siswa;
        $progress = SiswaProgress::firstOrCreate([
            'siswa_id' => $siswa->id,
            'materi_id' => $materi_id
        ]);

        if ($request->has('pdf_dibaca') || $request->input('type') === 'pdf') {
            $progress->pdf_dibaca = true;
        }
        if ($request->has('video_ditonton') || $request->input('type') === 'video') {
            $progress->video_ditonton = true;
        }
        if ($request->has('cerita_reflektif')) {
            $progress->cerita_reflektif = $request->cerita_reflektif;
        }
        
        // Jika materi tidak punya soal pretest, langsung bisa ditandai completed jika ketiganya (yang ada) terpenuhi
        $materi = Materi::findOrFail($materi_id);
        if ($materi->pretest_questions()->count() == 0) {
            $needsPdf = $materi->file_pdf ? $progress->pdf_dibaca : true;
            $needsVideo = $materi->url_youtube ? $progress->video_ditonton : true;
            $needsRefleksi = !empty($progress->cerita_reflektif) || $request->has('cerita_reflektif'); 
            
            // Anggap cerita reflektif itu wajib jika tidak ada pretest. Wait, mari kita buat reflektif opsional untuk unlock jika tidak wajib, tapi krn user minta ditambahkan, mari kita cek jika isian reflektif ada.
            // Sebenarnya jika tidak ada Pre-Test, maka cukup lengkapi baca dan tonton. Cerita reflektif disimpan saja.
            if ($needsPdf && $needsVideo && !empty($progress->cerita_reflektif)) {
                $progress->is_completed = true;
            }
        }

        $progress->save();

        return redirect()->route('siswa.mapels.show', ['mapel' => $mapel_id, 'materi_id' => $materi_id])->with('success', 'Progres belajar berhasil disimpan.');
    }

    /**
     * Tampilkan halaman Pretest
     */
    public function showPretest($mapel_id, $materi_id)
    {
        $siswa = Auth::user()->siswa;
        $mapel = Mapel::findOrFail($mapel_id);
        $materi = Materi::with('pretest_questions')->where('mapel_id', $mapel_id)->findOrFail($materi_id);
        $progress = SiswaProgress::where('siswa_id', $siswa->id)->where('materi_id', $materi_id)->firstOrFail();

        // Validasi: Harus sudah baca PDF, Tonton video, dan isi reflektif jika tersedia
        if ($materi->file_pdf && !$progress->pdf_dibaca) {
            return back()->with('error', 'Selesaikan membaca materi PDF terlebih dahulu.');
        }
        if ($materi->url_youtube && !$progress->video_ditonton) {
            return back()->with('error', 'Selesaikan menonton video terlebih dahulu.');
        }
        if (empty($progress->cerita_reflektif)) {
            return back()->with('error', 'Anda harus mengisi Cerita Reflektif terlebih dahulu.');
        }

        if ($progress->is_completed && $progress->pretest_nilai !== null) {
            return redirect()->route('siswa.mapels.show', $mapel_id)
                             ->with('info', 'Anda sudah menyelesaikan pre-test ini dengan nilai: ' . $progress->pretest_nilai);
        }

        return view('siswa.belajar.pretest', compact('mapel', 'materi', 'progress'));
    }

    /**
     * Hitung nilai Pretest
     */
    public function submitPretest(Request $request, $mapel_id, $materi_id)
    {
        $siswa = Auth::user()->siswa;
        $materi = Materi::with('pretest_questions')->findOrFail($materi_id);
        $progress = SiswaProgress::where('siswa_id', $siswa->id)->where('materi_id', $materi_id)->firstOrFail();

        $totalQuestions = $materi->pretest_questions->count();
        if ($totalQuestions == 0) {
            return back();
        }

        $correct = 0;
        foreach ($materi->pretest_questions as $question) {
            $answer = $request->input('jawaban_' . $question->id);
            if ($answer == $question->jawaban_benar) {
                $correct++;
            }
        }

        $score = round(($correct / $totalQuestions) * 100);

        $progress->pretest_nilai = $score;
        $progress->is_completed = true; // Langsung dianggap selesai berapapun nilainya
        $progress->save();

        return redirect()->route('siswa.mapels.show', $mapel_id)
                         ->with('success', 'Selamat! Anda menyelesaikan materi ini. Nilai Post-Test Anda: ' . $score);
    }
}
