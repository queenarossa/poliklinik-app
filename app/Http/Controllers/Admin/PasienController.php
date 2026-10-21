<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
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

        // Riwayat pendaftaran milik pasien yang sedang login
        $pendaftarans = DaftarPoli::with(['jadwalPeriksa.dokter.poli', 'periksas'])
            ->where('id_pasien', $user->id)
            ->orderByDesc('id')
            ->get();

        return view('pasien.daftar', compact('user', 'polis', 'jadwals', 'pendaftarans'));
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

        // Cegah daftar dua kali di jadwal yang sama selama belum diperiksa
        $sudahTerdaftar = DaftarPoli::where('id_pasien', $idPasien)
            ->where('id_jadwal', $jadwal->id)
            ->whereDoesntHave('periksas')
            ->exists();

        if ($sudahTerdaftar) {
            return back()
                ->withInput()
                ->with('error', 'Anda sudah terdaftar pada jadwal ini dan belum diperiksa.');
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

        return redirect()
            ->route('pasien.daftar')
            ->with('success', 'Berhasil mendaftar ke poli.');
    }
}