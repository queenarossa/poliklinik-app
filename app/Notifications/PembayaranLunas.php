<?php

namespace App\Notifications;

use App\Models\Periksa;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

/**
 * Untuk PASIEN: pembayarannya sudah diverifikasi (lunas) oleh admin.
 */
class PembayaranLunas extends Notification
{
    public function __construct(public Periksa $periksa)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $tanggal = Carbon::parse($this->periksa->tgl_periksa)->locale('id')->translatedFormat('d F Y');

        return [
            'judul'      => 'Pembayaran lunas',
            'pesan'      => "Pembayaran Anda untuk pemeriksaan tanggal {$tanggal} telah diverifikasi admin (lunas).",
            'periksa_id' => $this->periksa->id,
            'kunci'      => self::kunci($this->periksa),
        ];
    }

    public static function kunci(Periksa $periksa): string
    {
        return 'lunas:' . $periksa->id;
    }

    /**
     * Kirim ke pasien. Hanya terkirim SEKALI per pemeriksaan.
     */
    public static function kirimSekali(Periksa $periksa): bool
    {
        $pasien = $periksa->daftarPoli?->pasien;

        if (! $pasien) {
            return false;
        }

        $sudahAda = $pasien->notifications()
            ->where('type', self::class)
            ->where('data->kunci', self::kunci($periksa))
            ->exists();

        if ($sudahAda) {
            return false;
        }

        $pasien->notify(new self($periksa));

        return true;
    }
}