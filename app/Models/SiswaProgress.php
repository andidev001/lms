<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaProgress extends Model
{
    use HasFactory;

    protected $table = 'siswa_progress';

    protected $fillable = [
        'siswa_id',
        'materi_id',
        'pdf_dibaca',
        'video_ditonton',
        'is_completed',
        'cerita_reflektif',
        'pretest_nilai'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }
}
