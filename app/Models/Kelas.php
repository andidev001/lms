<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = ['kode_kelas', 'nama_kelas', 'guru_id', 'tahun_ajaran_id', 'keterangan'];

    public function wali_kelas()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function mapels()
    {
        return $this->belongsToMany(Mapel::class, 'kelas_mapel');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }
}
