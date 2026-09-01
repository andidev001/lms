<?php

namespace App\Imports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class GuruImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan NIP tidak kosong
        if (!isset($row['nip'])) {
            return null;
        }

        // Cek apakah Guru dengan NIP ini sudah ada, jika ada skip
        $guruExists = Guru::where('nip', $row['nip'])->exists();
        
        if ($guruExists) {
            return null;
        }

        return new Guru([
            'nip' => $row['nip'],
            'nama' => $row['nama'] ?? '-',
            'jenis_kelamin' => $row['jenis_kelamin'] ?? 'Laki-laki',
            'telepon' => $row['telepon'] ?? null,
        ]);
    }
}
