<?php

namespace App\Events;

use App\Models\DaftarPoli;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Dikirim lewat Laravel Reverb setiap nomor antrian yang sedang dilayani berubah.
 * ShouldBroadcastNow = langsung dikirim, tidak perlu queue worker.
 */
class AntrianDiperbarui implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public int $idJadwal;
    public ?int $dilayani;

    public function __construct(int $idJadwal)
    {
        $this->idJadwal = $idJadwal;

        // Nomor yang sedang dilayani = nomor antrian terkecil yang belum diperiksa
        $nomor = DaftarPoli::where('id_jadwal', $idJadwal)
            ->whereDoesntHave('periksas')
            ->min('no_antrian');

        $this->dilayani = $nomor !== null ? (int) $nomor : null;
    }

    public function broadcastOn(): array
    {
        return [new Channel('antrian')];
    }

    public function broadcastAs(): string
    {
        return 'antrian.diperbarui';
    }

    public function broadcastWith(): array
    {
        return [
            'id_jadwal' => $this->idJadwal,
            'dilayani'  => $this->dilayani,
        ];
    }

    /**
     * Pakai method ini (bukan event() langsung) supaya kalau Reverb belum
     * dijalankan, proses utama (simpan pemeriksaan / pendaftaran) tidak ikut error.
     */
    public static function kirim(int $idJadwal): void
    {
        try {
            event(new static($idJadwal));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
