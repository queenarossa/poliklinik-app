<?php

namespace App\Notifications;

use App\Models\Periksa;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

/**
 * Untuk DOKTER: pasien yang ia periksa mengunggah bukti pembayaran.
 */
class BuktiBayarDiunggah extends Notification
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
        $pasien = $this->periksa->daftarPoli->pasien->nama ?? 'Pasien';
        $tanggal = Carbon::parse($this->periksa->tgl_periksa)->locale('id')->translatedFormat('d F Y');

        return [
            'judul'      => 'Bukti pembayaran diunggah',
            'pesan'      => "{$pasien} telah mengunggah bukti pembayaran untuk pemeriksaan tanggal {$tanggal}.",
            'periksa_id' => $this->periksa->id,
            'kunci'      => self::kunci($this->periksa),
        ];
    }

    // Penanda unik satu kejadian (dipakai untuk mencegah notifikasi ganda)
    public static function kunci(Periksa $periksa): string
    {
        return 'bukti-bayar:' . $periksa->id;
    }

    /**
     * Kirim ke dokter yang memeriksa. Hanya terkirim SEKALI per pemeriksaan.
     * Mengembalikan true jika notifikasi baru dibuat, false jika sudah pernah dikirim.
     */
    public static function kirimSekali(Periksa $periksa): bool
    {
        $dokter = $periksa->daftarPoli?->jadwalPeriksa?->dokter;

        if (! $dokter) {
            return false;
        }

        $sudahAda = $dokter->notifications()
            ->where('type', self::class)
            ->where('data->kunci', self::kunci($periksa))
            ->exists();

        if ($sudahAda) {
            return false;
        }

        $dokter->notify(new self($periksa));

        return true;
    }
}