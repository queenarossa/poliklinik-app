<?php

namespace App\Exports;

use App\Models\Periksa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormats;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Riwayat pasien yang pernah diperiksa oleh dokter yang sedang login saja.
 */
class RiwayatPasienExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormats
{
    private int $no = 0;

    private const STATUS = [
        'belum_bayar'         => 'Belum Dibayar',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'lunas'               => 'Lunas',
    ];

    public function __construct(private int $idDokter)
    {
    }

    public function collection()
    {
        return Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($q) {
            $q->where('id_dokter', $this->idDokter);
        })->with([
            'daftarPoli.pasien',
            'detailPeriksas.obat',
        ])->orderBy('tgl_periksa', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal Periksa', 'No Antrian', 'Nama Pasien', 'Keluhan',
            'Catatan', 'Obat', 'Biaya (Rp)', 'Status Pembayaran',
        ];
    }

    public function map($periksa): array
    {
        $obat = $periksa->detailPeriksas
            ->map(fn ($d) => $d->obat->nama_obat ?? null)
            ->filter()
            ->implode(', ');

        return [
            ++$this->no,
            $periksa->tgl_periksa ? $periksa->tgl_periksa->format('d-m-Y H:i') : '-',
            $periksa->daftarPoli->no_antrian ?? '-',
            $periksa->daftarPoli->pasien->nama ?? '-',
            $periksa->daftarPoli->keluhan ?? '-',
            $periksa->catatan ?? '-',
            $obat !== '' ? $obat : '-',
            (int) $periksa->biaya_periksa,
            self::STATUS[$periksa->status_bayar] ?? $periksa->status_bayar,
        ];
    }

    public function columnFormats(): array
    {
        return ['H' => '#,##0'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
