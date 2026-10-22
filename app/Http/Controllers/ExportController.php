<?php

namespace App\Http\Controllers;

use App\Exports\DokterExport;
use App\Exports\JadwalPeriksaExport;
use App\Exports\ObatExport;
use App\Exports\PasienExport;
use App\Exports\RiwayatPasienExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    private function nama(string $dasar): string
    {
        return $dasar . '-' . now()->format('Ymd-His') . '.xlsx';
    }

    // ===== Role Admin =====
    public function dokter()
    {
        return Excel::download(new DokterExport, $this->nama('data-dokter'));
    }

    public function pasien()
    {
        return Excel::download(new PasienExport, $this->nama('data-pasien'));
    }

    public function obat()
    {
        return Excel::download(new ObatExport, $this->nama('data-obat'));
    }

    // ===== Role Dokter (hanya data milik dokter yang login) =====
    public function jadwalPeriksa()
    {
        return Excel::download(new JadwalPeriksaExport(Auth::id()), $this->nama('jadwal-periksa'));
    }

    public function riwayatPasien()
    {
        return Excel::download(new RiwayatPasienExport(Auth::id()), $this->nama('riwayat-pasien'));
    }
}
