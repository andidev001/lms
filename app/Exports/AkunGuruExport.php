<?php

namespace App\Exports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AkunGuruExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private $index = 0;

    public function collection()
    {
        return Guru::with('user')->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP / Identitas',
            'Nama Guru',
            'Email Login (Username)',
            'Password Default',
            'Status Akun'
        ];
    }

    public function map($row): array
    {
        $this->index++;
        $email = $row->user ? $row->user->email : 'Belum Ada Akun';
        $status = $row->user ? 'Aktif' : 'Belum Dibuat';
        $password = $row->user ? 'password (jika belum diubah)' : '- (Buat akun di Kelola Guru)';

        return [
            $this->index,
            $row->nip ?? '-',
            $row->nama,
            $email,
            $password,
            $status
        ];
    }
}
