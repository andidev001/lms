<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Sekolah::count() == 0) {
            Sekolah::create([
                'nama_sekolah' => 'LMS Sekolahku',
                'alamat' => 'Jl. Pendidikan No. 1, Jakarta',
                'telepon' => '021-12345678',
                'email' => 'info@sekolahku.sch.id'
            ]);
        }
    }
}
