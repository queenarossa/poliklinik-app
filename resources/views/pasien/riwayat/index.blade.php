<x-layouts.app title="Riwayat Pendaftaran">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Riwayat Pendaftaran</h2>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Jadwal</th>
                            <th class="px-6 py-4">Tgl Daftar</th>
                            <th class="px-6 py-4 text-center">No Antrian</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($pendaftarans as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-700">
                                {{ $p->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $p->jadwalPeriksa->dokter->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $p->jadwalPeriksa->hari ?? '-' }},
                                {{ \Carbon\Carbon::parse($p->jadwalPeriksa->jam_mulai)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($p->jadwalPeriksa->jam_selesai)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $p->created_at->format('d-m-Y') }}
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-800">
                                {{ $p->no_antrian }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($p->periksas_exists)
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-600">
                                    Sudah Diperiksa
                                </span>
                                @else
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-600">
                                    Menunggu
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($p->periksas_exists)
                                <a href="{{ route('pasien.riwayat.show', $p->id) }}"
                                    class="btn btn-sm bg-primary hover:bg-primary/90 text-white border-none rounded-lg px-4">
                                    <i class="fas fa-eye"></i>
                                    Detail
                                </a>
                                @else
                                <span class="text-xs text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-14 text-slate-400">
                                <i class="fas fa-clock-rotate-left text-3xl mb-3 block"></i>
                                Belum ada riwayat pendaftaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>