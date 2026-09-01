<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'mapels';
    protected $fillable = ['kode_mapel', 'nama_mapel', 'kategori', 'guru_id', 'keterangan'];

    public function guru_pengampu()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mapel');
    }

    public function materis()
    {
        return $this->hasMany(Materi::class, 'mapel_id');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'mapel_id');
    }
}
