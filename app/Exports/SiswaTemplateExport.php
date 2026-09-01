<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'nis',
            'nama',
            'jenis_kelamin',
            'nama_kelas'
        ];
    }

    public function array(): array
    {
        return [
            ['23101', 'Andi Siswanto', 'Laki-laki', 'X MIPA 1'],
            ['23102', 'Budi Santoso', 'Laki-laki', 'X MIPA 2'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
