<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use App\Notifications\PembayaranLunas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    private const FILTER = ['menunggu_verifikasi', 'lunas', 'belum_bayar', 'semua'];

    public function index(Request $request)
    {
        $filter = $request->query('status', 'menunggu_verifikasi');
        if (! in_array($filter, self::FILTER, true)) {
            $filter = 'menunggu_verifikasi';
        }

        $query = Periksa::with(['daftarPoli.pasien', 'daftarPoli.jadwalPeriksa.dokter.poli']);

        if ($filter !== 'semua') {
            $query->where('status_bayar', $filter);
        }

        $tagihans = $query->orderByDesc('tgl_upload_bukti')->orderByDesc('tgl_periksa')->get();

        $jumlah = [
            'menunggu_verifikasi' => Periksa::where('status_bayar', Periksa::STATUS_MENUNGGU)->count(),
            'lunas'               => Periksa::where('status_bayar', Periksa::STATUS_LUNAS)->count(),
            'belum_bayar'         => Periksa::where('status_bayar', Periksa::STATUS_BELUM)->count(),
        ];

        return view('admin.pembayaran.index', compact('tagihans', 'filter', 'jumlah'));
    }

    public function show($id)
    {
        $periksa = Periksa::with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat',
            'verifikator',
        ])->findOrFail($id);

        return view('admin.pembayaran.show', compact('periksa'));
    }

    public function konfirmasi($id)
    {
        $periksa = Periksa::findOrFail($id);

        // Update atomik: hanya berhasil kalau statusnya masih "menunggu verifikasi".
        // Jadi klik ganda / dua admin sekaligus tidak menghasilkan notifikasi ganda.
        $berhasil = Periksa::where('id', $periksa->id)
            ->where('status_bayar', Periksa::STATUS_MENUNGGU)
            ->update([
                'status_bayar'      => Periksa::STATUS_LUNAS,
                'tgl_verifikasi'    => now(),
                'diverifikasi_oleh' => Auth::id(),
            ]);

        if (! $berhasil) {
            return back()->with('error', 'Tagihan ini tidak dalam status menunggu verifikasi.');
        }

        // Notifikasi ke pasien (hanya terkirim sekali per pemeriksaan)
        try {
            PembayaranLunas::kirimSekali($periksa->fresh());
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran dikonfirmasi. Status tagihan menjadi Lunas.');
    }

    public function bukti($id)
    {
        $periksa = Periksa::findOrFail($id);

        abort_unless($periksa->bukti_bayar && Storage::disk('local')->exists($periksa->bukti_bayar), 404);

        return Storage::disk('local')->response($periksa->bukti_bayar);
    }
}
