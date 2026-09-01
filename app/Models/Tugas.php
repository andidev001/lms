<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $fillable = [
        'mapel_id',
        'materi_id',
        'judul',
        'deskripsi',
        'file_lampiran',
        'tenggat_waktu'
    ];

    protected $casts = [
        'tenggat_waktu' => 'datetime'
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    public function submissions()
    {
        return $this->hasMany(TugasSubmission::class);
    }
}
