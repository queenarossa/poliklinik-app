<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use App\Notifications\BuktiBayarDiunggah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    // Hanya tagihan milik pasien yang sedang login
    private function tagihanSaya()
    {
        return Periksa::whereHas('daftarPoli', function ($q) {
            $q->where('id_pasien', Auth::id());
        });
    }

    public function index()
    {
        $tagihans = $this->tagihanSaya()
            ->with('daftarPoli.jadwalPeriksa.dokter.poli')
            ->orderByDesc('tgl_periksa')
            ->get();

        return view('pasien.pembayaran.index', compact('tagihans'));
    }

    public function show($id)
    {
        $periksa = $this->tagihanSaya()
            ->with(['daftarPoli.jadwalPeriksa.dokter.poli', 'detailPeriksas.obat'])
            ->findOrFail($id);

        return view('pasien.pembayaran.show', compact('periksa'));
    }

    public function upload(Request $request, $id)
    {
        $periksa = $this->tagihanSaya()->findOrFail($id);

        if ($periksa->status_bayar === Periksa::STATUS_LUNAS) {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'bukti_bayar.required' => 'Pilih foto bukti pembayaran terlebih dahulu.',
            'bukti_bayar.image'    => 'File harus berupa gambar.',
            'bukti_bayar.mimes'    => 'Format foto harus JPG, JPEG, atau PNG.',
            'bukti_bayar.max'      => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Disimpan di storage privat (tidak bisa diakses lewat URL langsung)
        $path = $request->file('bukti_bayar')->store('bukti-bayar', 'local');

        // Upload ulang: hapus foto lama
        if ($periksa->bukti_bayar) {
            Storage::disk('local')->delete($periksa->bukti_bayar);
        }

        $periksa->update([
            'bukti_bayar'      => $path,
            'status_bayar'     => Periksa::STATUS_MENUNGGU,
            'tgl_upload_bukti' => now(),
        ]);

        // Notifikasi ke dokter (hanya terkirim sekali per pemeriksaan)
        try {
            BuktiBayarDiunggah::kirimSekali($periksa);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('pasien.pembayaran.show', $periksa->id)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

    // Menampilkan foto bukti (hanya pemilik tagihan)
    public function bukti($id)
    {
        $periksa = $this->tagihanSaya()->findOrFail($id);

        abort_unless($periksa->bukti_bayar && Storage::disk('local')->exists($periksa->bukti_bayar), 404);

        return Storage::disk('local')->response($periksa->bukti_bayar);
    }
}
