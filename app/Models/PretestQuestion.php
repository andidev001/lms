<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PretestQuestion extends Model
{
    use HasFactory;

    protected $table = 'pretest_questions';

    protected $fillable = [
        'materi_id',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'jawaban_benar'
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }
}
