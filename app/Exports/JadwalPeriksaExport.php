<?php

namespace App\Exports;

use App\Models\JadwalPeriksa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Jadwal periksa milik dokter yang sedang login saja.
 */
class JadwalPeriksaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $no = 0;

    public function __construct(private int $idDokter)
    {
    }

    public function collection(): Collection
    {
        return JadwalPeriksa::where('id_dokter', $this->idDokter)->orderBy('hari')->get();
    }

    public function headings(): array
    {
        return ['No', 'Hari', 'Jam Mulai', 'Jam Selesai'];
    }

    public function map($jadwal): array
    {
        return [
            ++$this->no,
            $jadwal->hari,
            substr((string) $jadwal->jam_mulai, 0, 5),
            substr((string) $jadwal->jam_selesai, 0, 5),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}