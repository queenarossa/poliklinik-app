<?php

namespace App\Exports;

use App\Models\Obat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ObatExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    private int $no = 0;

    public function collection(): Collection
    {
        return Obat::orderBy('nama_obat')->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama Obat', 'Kemasan', 'Harga (Rp)', 'Stok'];
    }

    public function map($obat): array
    {
        return [
            ++$this->no,
            $obat->nama_obat,
            $obat->kemasan ?? '-',
            (int) $obat->harga,
            (int) $obat->stok,
        ];
    }

    public function columnFormats(): array
    {
        return ['D' => '#,##0', 'E' => '#,##0'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}