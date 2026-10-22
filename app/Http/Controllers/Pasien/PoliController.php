<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Events\AntrianDiperbarui;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoliController extends Controller
{
    public function get()
    {
        $user = Auth::user();

        // Pasien lama (mis. dari seeder) yang belum punya no. RM dibuatkan otomatis
        if (empty($user->no_rm)) {
            $user->no_rm = User::generateNoRm($user->created_at);
            $user->save();
        }
        $polis = Poli::all();
        $jadwals = JadwalPeriksa::with('dokter.poli')->get();

        $antrianAktif = $this->antrianAktif($user->id);

        return view('pasien.daftar-poli.create', compact('user', 'polis', 'jadwals', 'antrianAktif'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_poli'   => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan'   => 'required|string',
        ]);

        // Pastikan jadwal yang dipilih memang milik dokter di poli tersebut
        $jadwal = JadwalPeriksa::with('dokter')->findOrFail($request->id_jadwal);
        if ((int) optional($jadwal->dokter)->id_poli !== (int) $request->id_poli) {
            return back()
                ->withInput()
                ->with('error', 'Jadwal yang dipilih tidak sesuai dengan poli.');
        }

        // id pasien diambil dari user yang login, bukan dari form
        $idPasien = Auth::id();

        // Aturan: pasien hanya boleh punya satu antrian aktif (tidak bisa daftar ke dua poli
        // sekaligus) dan baru boleh mendaftar lagi setelah pemeriksaan selesai.
        if ($this->antrianAktif($idPasien)) {
            return back()
                ->withInput()
                ->with('error', 'Anda masih memiliki antrian aktif. Pendaftaran baru hanya bisa dilakukan setelah pemeriksaan selesai.');
        }

        DB::transaction(function () use ($request, $jadwal, $idPasien) {
            $antrianTerakhir = DaftarPoli::where('id_jadwal', $jadwal->id)
                ->lockForUpdate()
                ->max('no_antrian');

            DaftarPoli::create([
                'id_pasien'  => $idPasien,
                'id_jadwal'  => $jadwal->id,
                'keluhan'    => $request->keluhan,
                'no_antrian' => ($antrianTerakhir ?? 0) + 1,
            ]);
        });

        // Kabari dashboard pasien lewat Reverb (jika antrian kosong, nomor ini langsung dilayani)
        AntrianDiperbarui::kirim($jadwal->id);

        return redirect()
            ->route('pasien.daftar')
            ->with('success', 'Berhasil mendaftar ke poli.');
    }

    // Pendaftaran milik pasien yang belum selesai diperiksa
    private function antrianAktif($idPasien)
    {
        return DaftarPoli::where('id_pasien', $idPasien)
            ->whereDoesntHave('periksas')
            ->latest('id')
            ->first();
    }
}