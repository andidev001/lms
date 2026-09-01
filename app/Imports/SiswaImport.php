<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SiswaImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan NIS tidak kosong
        if (!isset($row['nis'])) {
            return null;
        }

        // Cek apakah Siswa dengan NIS ini sudah ada, jika ada skip
        $siswaExists = Siswa::where('nis', $row['nis'])->exists();
        
        if ($siswaExists) {
            return null;
        }

        $kelas_id = null;
        if (!empty($row['nama_kelas'])) {
            $kelas = \App\Models\Kelas::where('nama_kelas', trim($row['nama_kelas']))->first();
            if ($kelas) {
                $kelas_id = $kelas->id;
            }
        }

        return new Siswa([
            'nis' => $row['nis'],
            'nama' => $row['nama'] ?? '-',
            'jenis_kelamin' => $row['jenis_kelamin'] ?? 'Laki-laki',
            'kelas_id' => $kelas_id
        ]);
    }
}
