<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DokterExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $no = 0;

    public function collection()
    {
        return User::where('role', 'dokter')->with('poli')->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama Dokter', 'Email', 'No. KTP', 'No. HP', 'Alamat', 'Poli'];
    }

    public function map($dokter): array
    {
        return [
            ++$this->no,
            $dokter->nama,
            $dokter->email,
            (string) $dokter->no_ktp,   // teks agar angka panjang tidak berubah jadi notasi ilmiah
            (string) $dokter->no_hp,
            $dokter->alamat,
            $dokter->poli->nama_poli ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
