<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AkunSiswaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $kelas_id;
    private $index = 0;

    public function __construct($kelas_id = null)
    {
        $this->kelas_id = $kelas_id;
    }

    public function collection()
    {
        $query = Siswa::with(['user', 'kelas'])->orderBy('kelas_id')->orderBy('nama');
        if ($this->kelas_id) {
            $query->where('kelas_id', $this->kelas_id);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS / NISN',
            'Nama Siswa',
            'Kelas',
            'Email Login (Username)',
            'Password Default (NIS)',
            'Status Akun'
        ];
    }

    public function map($row): array
    {
        $this->index++;
        $email = $row->user ? $row->user->email : 'Belum Ada Akun';
        $status = $row->user ? 'Aktif' : 'Belum Dibuat';
        $password = $row->user ? ($row->nis ?? 'NIS Masing-masing') : '- (Buat akun di Kelola Siswa)';
        $kelas = $row->kelas ? $row->kelas->nama_kelas : '-';

        return [
            $this->index,
            $row->nis ?? '-',
            $row->nama,
            $kelas,
            $email,
            $password,
            $status
        ];
    }
}
