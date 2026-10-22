<x-layouts.app title="Pembayaran">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Verifikasi Pembayaran</h2>
    </div>

    {{-- Filter Status --}}
    @php
        $tabs = [
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'lunas'               => 'Lunas',
            'belum_bayar'         => 'Belum Dibayar',
            'semua'               => 'Semua',
        ];
    @endphp
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach ($tabs as $kunci => $label)
        <a href="{{ route('admin.pembayaran.index', ['status' => $kunci]) }}"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition
                   {{ $filter === $kunci ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            {{ $label }}
            @if (isset($jumlah[$kunci]))
            <span class="ml-1 text-xs opacity-80">({{ $jumlah[$kunci] }})</span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Poli / Dokter</th>
                            <th class="px-6 py-4">Tgl Periksa</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Tgl Upload</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($tagihans as $tagihan)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $tagihan->daftarPoli->pasien->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $tagihan->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                                <span class="text-slate-400">/ {{ $tagihan->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $tagihan->tgl_periksa->format('d-m-Y H:i') }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                Rp {{ number_format($tagihan->biaya_periksa, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $tagihan->tgl_upload_bukti ? $tagihan->tgl_upload_bukti->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @include('components.partials.status-bayar', ['status' => $tagihan->status_bayar])
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.pembayaran.show', $tagihan->id) }}"
                                    class="btn btn-sm bg-primary hover:bg-primary/90 text-white border-none rounded-lg px-4">
                                    <i class="fas fa-magnifying-glass"></i>
                                    {{ $tagihan->status_bayar === 'menunggu_verifikasi' ? 'Periksa Bukti' : 'Detail' }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-14 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Tidak ada tagihan pada filter ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>
