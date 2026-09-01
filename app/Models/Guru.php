<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nama',
        'jenis_kelamin',
        'telepon',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas_wali()
    {
        return $this->hasOne(Kelas::class, 'guru_id');
    }

    public function mapels()
    {
        return $this->hasMany(Mapel::class, 'guru_id');
    }
}
