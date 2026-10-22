<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Events\AntrianDiperbarui;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeriksaPasienController extends Controller
{
    // Biaya jasa dokter (ditambahkan ke total harga obat)
    private const BIAYA_JASA_DOKTER = 150000;

    // Daftar pasien milik dokter yang sedang login
    private function queryPasienDokter()
    {
        return DaftarPoli::whereHas('jadwalPeriksa', function ($q) {
            $q->where('id_dokter', Auth::id());
        });
    }

    public function index()
    {
        $daftarPasien = $this->queryPasienDokter()
            ->with(['pasien', 'periksas'])
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        // Hanya boleh membuka pasien milik dokter ini
        $daftarPoli = $this->queryPasienDokter()
            ->with(['pasien', 'periksas'])
            ->findOrFail($id);

        // Pasien yang sudah diperiksa tidak boleh diperiksa lagi
        if ($daftarPoli->periksas->isNotEmpty()) {
            return redirect()
                ->route('periksa-pasien.index')
                ->with('error', 'Pasien ini sudah diperiksa.');
        }

        $obats = Obat::all();
        $biayaJasa = self::BIAYA_JASA_DOKTER;

        return view('dokter.periksa-pasien.create', compact('daftarPoli', 'obats', 'biayaJasa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
            'obat'           => 'required|array|min:1',
            'obat.*'         => 'exists:obat,id',
            'catatan'        => 'nullable|string',
        ]);

        $daftarPoli = $this->queryPasienDokter()
            ->with('periksas')
            ->findOrFail($request->id_daftar_poli);

        if ($daftarPoli->periksas->isNotEmpty()) {
            return redirect()
                ->route('periksa-pasien.index')
                ->with('error', 'Pasien ini sudah diperiksa.');
        }

        $obatIds = array_unique($request->obat);

        DB::transaction(function () use ($request, $daftarPoli, $obatIds) {
            // Total dihitung di server dari harga obat di database
            $totalObat = Obat::whereIn('id', $obatIds)->sum('harga');

            $periksa = Periksa::create([
                'id_daftar_poli' => $daftarPoli->id,
                'tgl_periksa'    => now(),
                'catatan'        => $request->catatan,
                'biaya_periksa'  => $totalObat + self::BIAYA_JASA_DOKTER,
            ]);

            foreach ($obatIds as $idObat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat'    => $idObat,
                ]);
            }
        });

        // Kabari semua dashboard pasien lewat Reverb: nomor yang dilayani sudah maju
        AntrianDiperbarui::kirim($daftarPoli->id_jadwal);

        return redirect()
            ->route('periksa-pasien.index')
            ->with('success', 'Data periksa berhasil disimpan.');
    }
}