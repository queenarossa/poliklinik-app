<x-layouts.app title="Pembayaran">

    @php
        $totalObat = $periksa->detailPeriksas->sum(function ($d) {
            return $d->obat->harga ?? 0;
        });
        $biayaJasa = $periksa->biaya_periksa - $totalObat;
        $lunas = $periksa->status_bayar === 'lunas';
    @endphp

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pasien.pembayaran.index') }}" class="flex items-center justify-center w-9 h-9
                  rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Detail Tagihan</h2>
        <div class="ml-2">
            @include('components.partials.status-bayar', ['status' => $periksa->status_bayar])
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Rincian Tagihan --}}
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-6">
                <h3 class="font-bold text-slate-800 mb-4">Rincian Tagihan</h3>

                <div class="text-sm space-y-1 mb-4 text-slate-600">
                    <div class="flex justify-between">
                        <span>Tanggal Periksa</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->tgl_periksa->format('d-m-Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Poli</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Dokter</span>
                        <span class="font-semibold text-slate-800">{{ $periksa->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}</span>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-4 text-sm space-y-2">
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya jasa dokter</span>
                        <span>Rp {{ number_format($biayaJasa, 0, ',', '.') }}</span>
                    </div>

                    @foreach ($periksa->detailPeriksas as $detail)
                    <div class="flex justify-between text-slate-600">
                        <span>{{ $detail->obat->nama_obat ?? '-' }}
                            @if (! empty($detail->obat->kemasan))
                            <span class="text-slate-400">({{ $detail->obat->kemasan }})</span>
                            @endif
                        </span>
                        <span>Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach

                    <div class="flex justify-between font-bold text-slate-800 border-t border-slate-200 pt-3 text-base">
                        <span>Total Tagihan</span>
                        <span>Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if ($lunas)
                <div class="mt-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-xs text-green-700">
                    <i class="fas fa-circle-check mr-1"></i>
                    Pembayaran dikonfirmasi admin pada {{ $periksa->tgl_verifikasi?->format('d-m-Y H:i') }}.
                </div>
                @endif
            </div>
        </div>

        {{-- Bukti Pembayaran --}}
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-6">
                <h3 class="font-bold text-slate-800 mb-4">Bukti Pembayaran</h3>

                @if ($periksa->bukti_bayar)
                <div class="mb-4">
                    <p class="text-xs text-slate-500 mb-2">
                        Diunggah {{ $periksa->tgl_upload_bukti?->format('d-m-Y H:i') }}
                    </p>
                    <img src="{{ route('pasien.pembayaran.bukti', $periksa->id) }}" alt="Bukti pembayaran"
                        class="w-full max-h-80 object-contain rounded-lg border border-slate-200 bg-slate-50">
                </div>
                @endif

                @if (! $lunas)
                <form action="{{ route('pasien.pembayaran.upload', $periksa->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        {{ $periksa->bukti_bayar ? 'Ganti Foto Bukti Pembayaran' : 'Foto Bukti Pembayaran' }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="bukti_bayar" id="bukti_bayar" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required
                        class="w-full text-sm border rounded-lg p-2 bg-white
                               @error('bukti_bayar') border-red-500 @enderror">
                    <p class="text-[11px] text-slate-400 mt-1">Format JPG / PNG, maksimal 2 MB.</p>
                    @error('bukti_bayar')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    <img id="preview" class="hidden mt-3 w-full max-h-64 object-contain rounded-lg border border-slate-200 bg-slate-50" alt="Pratinjau">

                    <button type="submit" class="mt-4 px-5 py-2 rounded-lg bg-primary hover:bg-primary/90
                               text-white text-sm font-semibold transition">
                        <i class="fas fa-upload mr-1"></i>
                        {{ $periksa->bukti_bayar ? 'Unggah Ulang' : 'Unggah Bukti' }}
                    </button>
                </form>

                <script>
                    document.getElementById('bukti_bayar').addEventListener('change', function () {
                        const preview = document.getElementById('preview');
                        const file = this.files[0];
                        if (!file) { preview.classList.add('hidden'); return; }
                        preview.src = URL.createObjectURL(file);
                        preview.classList.remove('hidden');
                    });
                </script>
                @elseif (! $periksa->bukti_bayar)
                <p class="text-sm text-slate-400">Tidak ada bukti pembayaran.</p>
                @endif
            </div>
        </div>

    </div>

</x-layouts.app>
