<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PasienExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $no = 0;

    public function collection()
    {
        return User::where('role', 'pasien')->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return ['No', 'No. Rekam Medis', 'Nama Pasien', 'Email', 'No. KTP', 'No. HP', 'Alamat'];
    }

    public function map($pasien): array
    {
        return [
            ++$this->no,
            $pasien->no_rm ?? '-',
            $pasien->nama,
            $pasien->email,
            (string) $pasien->no_ktp,
            (string) $pasien->no_hp,
            $pasien->alamat,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
