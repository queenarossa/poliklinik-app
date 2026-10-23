<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\Periksa;
use Illuminate\Support\Facades\Auth;

class RiwayatPendaftaranController extends Controller
{
    /**
     * Semua pendaftaran milik pasien yang sedang login, terbaru dulu.
     *
     * Efisien (jumlah query tetap, tidak bertambah seiring jumlah baris):
     *  - with('jadwalPeriksa.dokter.poli') : eager loading, bukan query per baris
     *  - withExists('periksas')            : status diperiksa lewat satu subquery EXISTS,
     *                                        tanpa memuat data periksa satu per satu
     */
    public function index()
    {
        $pendaftarans = DaftarPoli::where('id_pasien', Auth::id())
            ->with('jadwalPeriksa.dokter.poli')
            ->withExists('periksas')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('pasien.riwayat.index', compact('pendaftarans'));
    }

    /**
     * Detail pemeriksaan untuk satu pendaftaran: catatan dokter, daftar obat, total biaya.
     * {id} adalah id pendaftaran (daftar_poli).
     */
    public function show($id)
    {
        // Hanya pendaftaran milik pasien yang login (milik orang lain -> 404)
        $pendaftaran = DaftarPoli::where('id_pasien', Auth::id())->findOrFail($id);

        $periksa = Periksa::where('id_daftar_poli', $pendaftaran->id)
            ->with(['daftarPoli.jadwalPeriksa.dokter.poli', 'detailPeriksas.obat'])
            ->first();

        // Belum diperiksa -> belum ada detail
        if (! $periksa) {
            return redirect()
                ->route('pasien.riwayat.index')
                ->with('error', 'Pendaftaran ini belum diperiksa, jadi belum ada detail pemeriksaan.');
        }

        return view('pasien.riwayat.show', compact('periksa'));
    }
}