<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;
    
    protected $table = 'materis';

    protected $fillable = ['mapel_id', 'judul', 'deskripsi', 'urutan', 'file_pdf', 'url_youtube'];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function pretest_questions()
    {
        return $this->hasMany(PretestQuestion::class);
    }

    public function siswa_progress()
    {
        return $this->hasMany(SiswaProgress::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'materi_id');
    }
}
