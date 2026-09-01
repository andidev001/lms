<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\PretestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PretestController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $mapel_id, $materi_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }
        
        $materi = Materi::where('id', $materi_id)->where('mapel_id', $mapel_id)->firstOrFail();

        $this->validate($request, [
            'pertanyaan' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'opsi_e' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D,E',
        ]);

        $requestData = $request->all();
        $requestData['materi_id'] = $materi->id;
        
        PretestQuestion::create($requestData);

        return redirect()->route('guru.mapels.materis.show', [$mapel_id, $materi_id])
                        ->with('success', 'Soal berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $mapel_id, $materi_id, $id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }

        $this->validate($request, [
            'pertanyaan' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'opsi_e' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D,E',
        ]);

        $question = PretestQuestion::where('materi_id', $materi_id)->findOrFail($id);
        $question->update($request->all());

        return redirect()->route('guru.mapels.materis.show', [$mapel_id, $materi_id])
                        ->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($mapel_id, $materi_id, $id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }

        $question = PretestQuestion::where('materi_id', $materi_id)->findOrFail($id);
        $question->delete();

        return redirect()->route('guru.mapels.materis.show', [$mapel_id, $materi_id])
                        ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Download template format Word untuk import soal.
     */
    public function downloadTemplate($mapel_id, $materi_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }

        $htmlContent = '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body style="font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.6;">
<h2 style="color: #2563eb; margin-bottom: 0px;">TEMPLATE & PANDUAN IMPORT SOAL (WORD / TXT)</h2>
<p style="color: #64748b; margin-top: 5px;">Gunakan format penulisan di bawah ini untuk mengimpor puluhan atau ratusan soal sekaligus ke dalam sistem LMS.</p>
<hr style="border: 1px solid #cbd5e1;">
<p><strong>PETUNJUK PENGISIAN:</strong></p>
<ul style="line-height: 1.8;">
    <li>Setiap awal soal <strong>wajib</strong> diawali dengan kode <strong>S:</strong> atau <strong>S:1)</strong></li>
    <li>Pilihan opsi jawaban <strong>wajib</strong> ditulis berurutan dari A sampai E dengan format: <strong>A:)</strong>, <strong>B:)</strong>, <strong>C:)</strong>, <strong>D:)</strong>, dan <strong>E:)</strong></li>
    <li>Kunci jawaban ditulis pada baris terakhir dari setiap soal dengan kode: <strong>JAWABAN: A</strong> (pilih salah satu huruf A/B/C/D/E)</li>
    <li>Anda dapat menyalin contoh di bawah dan mengganti teks pertanyaannya. Setelah selesai, simpan file sebagai <strong>.docx</strong>, <strong>.doc</strong>, atau <strong>.txt</strong>, kemudian upload di LMS.</li>
</ul>
<hr style="border: 1px solid #cbd5e1;">
<br>

<p>
S:1) Jaringan komputer yang mencakup area sangat luas disebut ....<br>
A:) LAN<br>
B:) MAN<br>
C:) WAN<br>
D:) PAN<br>
E:) WLAN<br>
JAWABAN: C
</p>
<br>
<p>
S:2) Perangkat lunak yang berfungsi sebagai sistem operasi komputer adalah ....<br>
A:) Microsoft Word<br>
B:) Linux Ubuntu<br>
C:) Google Chrome<br>
D:) Adobe Photoshop<br>
E:) Corel Draw<br>
JAWABAN: B
</p>
<br>
<p>
S:3) Tombol pintasan (shortcut) pada keyboard yang digunakan untuk menyalin teks yang di-copy adalah ....<br>
A:) Ctrl + C<br>
B:) Ctrl + X<br>
C:) Ctrl + V<br>
D:) Ctrl + Z<br>
E:) Ctrl + S<br>
JAWABAN: C
</p>
</body>
</html>';

        return response($htmlContent)
                ->header('Content-Type', 'application/msword; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="Template_Import_Soal_PostTest.doc"');
    }

    /**
     * Import soal dari file Word (.docx, .doc, atau .txt).
     */
    public function importWord(Request $request, $mapel_id, $materi_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        if ($mapel->guru_id != Auth::user()->guru->id) {
            abort(403);
        }

        $materi = Materi::where('id', $materi_id)->where('mapel_id', $mapel_id)->firstOrFail();

        if (!$request->hasFile('file_word')) {
            return redirect()->route('guru.mapels.materis.show', [$mapel_id, $materi_id])
                             ->with('error', 'File tidak terdeteksi atau ukuran file melebihi batas upload server hosting Anda (coba periksa pengaturan upload_max_filesize di cPanel).');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file_word' => 'required|file|max:10240',
        ], [
            'file_word.required' => 'Silakan pilih file dokumen Word atau TXT terlebih dahulu.',
            'file_word.file' => 'Berkas yang diunggah harus berupa file yang valid.',
            'file_word.max' => 'Ukuran file maksimal adalah 10 MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('guru.mapels.materis.show', [$mapel_id, $materi_id])
                             ->with('error', $validator->errors()->first());
        }

        $file = $request->file('file_word');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['doc', 'docx', 'txt', 'rtf'])) {
            return redirect()->route('guru.mapels.materis.show', [$mapel_id, $materi_id])
                             ->with('error', 'Format file wajib berekstensi .doc, .docx, atau .txt. Ekstensi file Anda yang terdeteksi: .' . $extension);
        }

        $lines = [];
        $isParsedByZip = false;

        // Coba buka dengan ZipArchive terlebih dahulu (untuk file .docx atau .doc berbasis Office XML)
        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($file->getRealPath()) === true) {
                $xmlData = $zip->getFromName('word/document.xml');
                $zip->close();
                if ($xmlData) {
                    $isParsedByZip = true;
                    $text = str_replace(['</w:p>', '</w:tr>', '<w:br/>', '<w:br />', '<w:tab/>'], "\n", $xmlData);
                    $text = strip_tags($text);
                    $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    $rawLines = preg_split("/\r\n|\n|\r/", $text);
                    foreach ($rawLines as $line) {
                        if (trim($line) !== '') {
                            $lines[] = trim($line);
                        }
                    }
                }
            }
        }

        // Jika bukan file zip/xml (misal file .doc dari template unduhan kita, atau file .txt biasa)
        if (!$isParsedByZip) {
            $content = file_get_contents($file->getRealPath());
            // Hapus karakter null (biasanya muncul bila file berformat UTF-16 atau Word lama)
            $content = str_replace("\x00", "", $content);
            // Konversi tag baris baru HTML (jika file .doc kita disimpan langsung) menjadi new line \n
            $content = str_replace(['<br>', '<br/>', '<br />', '</p>', '</tr>', '</li>', '<p>', '</div>'], "\n", $content);
            $content = strip_tags($content);
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            $rawLines = preg_split("/\r\n|\n|\r/", $content);
            foreach ($rawLines as $line) {
                if (trim($line) !== '') {
                    $lines[] = trim($line);
                }
            }
        }

        if (empty($lines)) {
            return redirect()->route('guru.mapels.materis.show', [$mapel_id, $materi_id])
                             ->with('error', 'Gagal membaca isi dokumen. Pastikan berkas Word/TXT Anda berisi teks soal dengan format yang benar.');
        }

        $questions = [];
        $currentQuestion = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Deteksi awal soal: S:1), S: 1), S:1., S: 1., S:1, atau S:
            if (preg_match('/^S\s*:\s*\d*\s*[)\.\-]?\s*(.*)$/i', $line, $matches)) {
                if ($currentQuestion && !empty($currentQuestion['pertanyaan']) && !empty($currentQuestion['opsi_a'])) {
                    $questions[] = $currentQuestion;
                }
                $currentQuestion = [
                    'materi_id' => $materi->id,
                    'pertanyaan' => trim($matches[1]),
                    'opsi_a' => '',
                    'opsi_b' => '',
                    'opsi_c' => '',
                    'opsi_d' => '',
                    'opsi_e' => '',
                    'jawaban_benar' => 'A' // Nilai default
                ];
            }
            // Deteksi opsi: A:) LAN atau B:) MAN atau C) WAN
            elseif ($currentQuestion && preg_match('/^([A-E])\s*(?::\s*[)\.\-]|[)\.\-])\s*(.*)$/i', $line, $matches)) {
                $key = strtolower(trim($matches[1]));
                $val = trim($matches[2]);
                $currentQuestion["opsi_{$key}"] = $val;
            }
            // Deteksi kunci jawaban: JAWABAN: C atau KUNCI: C atau ANSWER: C
            elseif ($currentQuestion && preg_match('/^(?:JAWABAN|KUNCI|ANSWER)\s*:\s*([A-E])/i', $line, $matches)) {
                $currentQuestion['jawaban_benar'] = strtoupper(trim($matches[1]));
                $questions[] = $currentQuestion;
                $currentQuestion = null;
            }
            // Kelanjutan teks soal jika terpisah baris
            elseif ($currentQuestion && empty($currentQuestion['opsi_a'])) {
                $currentQuestion['pertanyaan'] .= '<br>' . $line;
            }
        }

        if ($currentQuestion && !empty($currentQuestion['pertanyaan']) && !empty($currentQuestion['opsi_a'])) {
            $questions[] = $currentQuestion;
        }

        if (empty($questions)) {
            return redirect()->route('guru.mapels.materis.show', [$mapel_id, $materi_id])
                             ->with('error', 'Tidak ada soal valid yang terdeteksi di dalam file. Pastikan setiap soal diawali dengan "S:1)", opsi "A:)", dan "JAWABAN: [Kunci]".');
        }

        $count = 0;
        foreach ($questions as $q) {
            PretestQuestion::create($q);
            $count++;
        }

        return redirect()->route('guru.mapels.materis.show', [$mapel_id, $materi_id])
                         ->with('success', "Berhasil mengimpor {$count} soal dari file dokumen!");
    }
}
