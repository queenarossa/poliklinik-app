<x-layouts.app title="Detail Pemeriksaan">

    @php
        $totalObat = $periksa->detailPeriksas->sum(function ($d) {
            return $d->obat->harga ?? 0;
        });
        $biayaJasa = $periksa->biaya_periksa - $totalObat;
        $jadwal = $periksa->daftarPoli->jadwalPeriksa;
    @endphp

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pasien.riwayat.index') }}" class="flex items-center justify-center w-9 h-9
                  rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Detail Pemeriksaan</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Informasi + Catatan Dokter --}}
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-6">
                <h3 class="font-bold text-slate-800 mb-4">Informasi Pemeriksaan</h3>

                <div class="text-sm space-y-2 text-slate-600 mb-5">
                    <div class="flex justify-between">
                        <span>Poli</span>
                        <span class="font-semibold text-slate-800">{{ $jadwal->dokter->poli->nama_poli ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Dokter</span>
                        <span class="font-semibold text-slate-800">{{ $jadwal->dokter->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Jadwal</span>
                        <span class="font-semibold text-slate-800">
                            {{ $jadwal->hari }},
                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                            - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>No Antrian</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->daftarPoli->no_antrian }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal Periksa</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->tgl_periksa->format('d-m-Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span>Keluhan</span>
                        <span class="font-semibold text-slate-800 text-right">{{ $periksa->daftarPoli->keluhan }}</span>
                    </div>
                </div>

                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Catatan Dokter</h4>
                <div class="px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 text-sm text-slate-700 whitespace-pre-line">{{ $periksa->catatan ?: 'Tidak ada catatan.' }}</div>
            </div>
        </div>

        {{-- Obat + Biaya --}}
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-6">
                <h3 class="font-bold text-slate-800 mb-4">Daftar Obat</h3>

                @forelse ($periksa->detailPeriksas as $detail)
                <div class="flex items-center justify-between px-4 py-2 mb-2 rounded-lg bg-slate-50 border border-slate-200 text-sm">
                    <span class="text-slate-700">
                        <span class="font-semibold">{{ $detail->obat->nama_obat ?? '-' }}</span>
                        @if (! empty($detail->obat->kemasan))
                        <span class="text-slate-400">({{ $detail->obat->kemasan }})</span>
                        @endif
                    </span>
                    <span class="text-slate-600">Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}</span>
                </div>
                @empty
                <p class="text-sm text-slate-400 mb-2">Tidak ada obat yang diresepkan.</p>
                @endforelse

                <div class="border-t border-slate-200 mt-4 pt-4 text-sm space-y-2">
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya jasa dokter</span>
                        <span>Rp {{ number_format($biayaJasa, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Total obat</span>
                        <span>Rp {{ number_format($totalObat, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-800 border-t border-slate-200 pt-3 text-base">
                        <span>Total Biaya</span>
                        <span>Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        Status pembayaran:
                        @include('components.partials.status-bayar', ['status' => $periksa->status_bayar])
                    </div>
                    @if ($periksa->status_bayar !== 'lunas')
                    <a href="{{ route('pasien.pembayaran.show', $periksa->id) }}"
                        class="text-xs font-semibold text-primary hover:underline">
                        Bayar sekarang <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>