<x-layouts.app title="Pembayaran">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Pembayaran</h2>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Tanggal Periksa</th>
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Total Tagihan</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($tagihans as $tagihan)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-500">
                                {{ $tagihan->tgl_periksa->format('d-m-Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $tagihan->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $tagihan->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                Rp {{ number_format($tagihan->biaya_periksa, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @include('components.partials.status-bayar', ['status' => $tagihan->status_bayar])
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($tagihan->status_bayar === 'lunas')
                                <a href="{{ route('pasien.pembayaran.show', $tagihan->id) }}"
                                    class="btn btn-sm btn-ghost text-slate-600 rounded-lg px-4">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                @else
                                <a href="{{ route('pasien.pembayaran.show', $tagihan->id) }}"
                                    class="btn btn-sm bg-primary hover:bg-primary/90 text-white border-none rounded-lg px-4">
                                    <i class="fas fa-upload"></i>
                                    {{ $tagihan->status_bayar === 'menunggu_verifikasi' ? 'Lihat / Ganti Bukti' : 'Upload Bukti' }}
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-14 text-slate-400">
                                <i class="fas fa-file-invoice-dollar text-3xl mb-3 block"></i>
                                Belum ada tagihan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>