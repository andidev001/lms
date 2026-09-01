<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasSubmission extends Model
{
    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'teks_jawaban',
        'file_jawaban',
        'link_jawaban',
        'waktu_pengumpulan',
        'nilai',
        'catatan_guru'
    ];

    protected $casts = [
        'waktu_pengumpulan' => 'datetime'
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
