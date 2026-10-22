<x-layouts.app title="Pembayaran">

    @php
        $totalObat = $periksa->detailPeriksas->sum(function ($d) {
            return $d->obat->harga ?? 0;
        });
        $biayaJasa = $periksa->biaya_periksa - $totalObat;
    @endphp

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.pembayaran.index') }}" class="flex items-center justify-center w-9 h-9
                  rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Detail Pembayaran</h2>
        <div class="ml-2">
            @include('components.partials.status-bayar', ['status' => $periksa->status_bayar])
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Rincian --}}
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-6">
                <h3 class="font-bold text-slate-800 mb-4">Rincian Tagihan</h3>

                <div class="text-sm space-y-1 mb-4 text-slate-600">
                    <div class="flex justify-between">
                        <span>Pasien</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->daftarPoli->pasien->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>No. Rekam Medis</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->daftarPoli->pasien->no_rm ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Poli</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Dokter</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal Periksa</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->tgl_periksa->format('d-m-Y H:i') }}</span>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-4 text-sm space-y-2">
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya jasa dokter</span>
                        <span>Rp {{ number_format($biayaJasa, 0, ',', '.') }}</span>
                    </div>

                    @foreach ($periksa->detailPeriksas as $detail)
                    <div class="flex justify-between text-slate-600">
                        <span>{{ $detail->obat->nama_obat ?? '-' }}</span>
                        <span>Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach

                    <div class="flex justify-between font-bold text-slate-800 border-t border-slate-200 pt-3 text-base">
                        <span>Total Tagihan</span>
                        <span>Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if ($periksa->status_bayar === 'lunas')
                <div class="mt-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-xs text-green-700">
                    <i class="fas fa-circle-check mr-1"></i>
                    Dikonfirmasi {{ $periksa->tgl_verifikasi?->format('d-m-Y H:i') }}
                    oleh {{ $periksa->verifikator->nama ?? 'admin' }}.
                </div>
                @endif
            </div>
        </div>

        {{-- Bukti --}}
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-6">
                <h3 class="font-bold text-slate-800 mb-4">Bukti Pembayaran</h3>

                @if ($periksa->bukti_bayar)
                <p class="text-xs text-slate-500 mb-2">
                    Diunggah {{ $periksa->tgl_upload_bukti?->format('d-m-Y H:i') }}
                </p>
                <a href="{{ route('admin.pembayaran.bukti', $periksa->id) }}" target="_blank">
                    <img src="{{ route('admin.pembayaran.bukti', $periksa->id) }}" alt="Bukti pembayaran"
                        class="w-full max-h-96 object-contain rounded-lg border border-slate-200 bg-slate-50">
                </a>
                <p class="text-[11px] text-slate-400 mt-1">Klik gambar untuk memperbesar.</p>
                @else
                <p class="text-sm text-slate-400">Pasien belum mengunggah bukti pembayaran.</p>
                @endif

                @if ($periksa->status_bayar === 'menunggu_verifikasi')
                <form action="{{ route('admin.pembayaran.konfirmasi', $periksa->id) }}" method="POST" class="mt-5">
                    @csrf
                    <button type="submit" onclick="return confirm('Konfirmasi pembayaran ini sebagai LUNAS?')"
                        class="px-5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition">
                        <i class="fas fa-circle-check mr-1"></i>
                        Konfirmasi Pembayaran (Lunas)
                    </button>
                </form>
                @endif
            </div>
        </div>

    </div>

</x-layouts.app>
