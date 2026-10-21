<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $antrianAktif = $this->antrianAktif();
        $dilayani = $this->nomorDilayani();

        $urutanHari = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];

        $jadwals = JadwalPeriksa::with('dokter.poli')->get()
            ->sort(function ($a, $b) use ($urutanHari) {
                return [data_get($a, 'dokter.poli.nama_poli', ''), $urutanHari[$a->hari] ?? 9, $a->jam_mulai]
                    <=> [data_get($b, 'dokter.poli.nama_poli', ''), $urutanHari[$b->hari] ?? 9, $b->jam_mulai];
            })
            ->values();

        return view('pasien.dashboard', compact('antrianAktif', 'dilayani', 'jadwals'));
    }

    /**
     * Endpoint JSON untuk live update (dipanggil berkala oleh JavaScript).
     */
    public function live()
    {
        $antrianAktif = $this->antrianAktif();

        return response()->json([
            'ada_antrian' => $antrianAktif !== null,
            'dilayani'    => $this->nomorDilayani(), // {id_jadwal: nomor}
        ]);
    }

    // Pendaftaran pasien yang belum selesai diperiksa
    private function antrianAktif(): ?DaftarPoli
    {
        return DaftarPoli::with('jadwalPeriksa.dokter.poli')
            ->where('id_pasien', Auth::id())
            ->whereDoesntHave('periksas')
            ->latest('id')
            ->first();
    }

    // Nomor yang sedang dilayani per jadwal = nomor antrian terkecil yang belum diperiksa
    private function nomorDilayani()
    {
        return DaftarPoli::whereDoesntHave('periksas')
            ->select('id_jadwal', DB::raw('MIN(no_antrian) as dilayani'))
            ->groupBy('id_jadwal')
            ->pluck('dilayani', 'id_jadwal');
    }
}