<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GuruProgressExport implements FromView, ShouldAutoSize
{
    protected $mapel;
    protected $kelas;
    protected $materis;
    protected $siswas;
    protected $progressMatrix;
    protected $tugases;
    protected $tugasMatrix;

    public function __construct($mapel, $kelas, $materis, $siswas, $progressMatrix, $tugases, $tugasMatrix)
    {
        $this->mapel = $mapel;
        $this->kelas = $kelas;
        $this->materis = $materis;
        $this->siswas = $siswas;
        $this->progressMatrix = $progressMatrix;
        $this->tugases = $tugases;
        $this->tugasMatrix = $tugasMatrix;
    }

    public function view(): View
    {
        return view('guru.progress.export', [
            'mapel' => $this->mapel,
            'kelas' => $this->kelas,
            'materis' => $this->materis,
            'siswas' => $this->siswas,
            'progressMatrix' => $this->progressMatrix,
            'tugases' => $this->tugases,
            'tugasMatrix' => $this->tugasMatrix
        ]);
    }
}
