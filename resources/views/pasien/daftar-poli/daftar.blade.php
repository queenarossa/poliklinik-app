<x-layouts.app title="Daftar Poli">

    {{-- Form Pendaftaran --}}
    <div class="max-w-xl mx-auto">
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-8">

                <h2 class="text-xl font-bold text-slate-800 text-center mb-6">
                    <i class="fas fa-clipboard-list text-primary mr-1"></i>
                    Pendaftaran Poli
                </h2>

                <form action="{{ route('pasien.daftar.submit') }}" method="POST">
                    @csrf

                    {{-- Nomor Rekam Medis --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Rekam Medis</label>
                        <input type="text" value="{{ $user->no_rm }}" disabled
                            class="w-full px-3 py-2 text-sm border rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed">
                    </div>

                    {{-- Pilih Poli --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Poli</label>
                        <select name="id_poli" id="id_poli" required
                            class="w-full px-3 py-2 text-sm border rounded-lg bg-white
                                   focus:border-primary focus:outline-none
                                   @error('id_poli') border-red-500 @enderror">
                            <option value="">-- Pilih Poli --</option>
                            @foreach ($polis as $poli)
                            <option value="{{ $poli->id }}" @selected(old('id_poli') == $poli->id)>
                                {{ $poli->nama_poli }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_poli')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pilih Jadwal Periksa --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Jadwal Periksa</label>
                        <select name="id_jadwal" id="id_jadwal" required
                            class="w-full px-3 py-2 text-sm border rounded-lg bg-white
                                   focus:border-primary focus:outline-none
                                   @error('id_jadwal') border-red-500 @enderror">
                            <option value="">-- Pilih Jadwal --</option>
                        </select>
                        @error('id_jadwal')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Keluhan --}}
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keluhan</label>
                        <textarea name="keluhan" rows="3" required placeholder="Tulis keluhan anda..."
                            class="w-full px-3 py-2 text-sm border rounded-lg
                                   focus:border-primary focus:outline-none
                                   @error('keluhan') border-red-500 @enderror">{{ old('keluhan') }}</textarea>
                        @error('keluhan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2 rounded-lg bg-primary hover:bg-primary/90
                                   text-white text-sm font-semibold transition">
                            Daftar Poli
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Pendaftaran Saya (hapus blok ini kalau tidak diperlukan) --}}
    <div class="max-w-4xl mx-auto mt-8">
        <h3 class="text-base font-bold text-slate-800 mb-3">Pendaftaran Saya</h3>

        <div class="card bg-base-100 shadow-md rounded-2 border">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Poli</th>
                                <th class="px-6 py-4">Dokter</th>
                                <th class="px-6 py-4">Jadwal</th>
                                <th class="px-6 py-4">No Antrian</th>
                                <th class="px-6 py-4 text-right">Status</th>
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
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $p->no_antrian }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if ($p->periksas->isNotEmpty())
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-600">
                                        Sudah Diperiksa
                                    </span>
                                    @else
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-600">
                                        Menunggu
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-slate-400">
                                    <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                    Belum ada pendaftaran
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @php
        $jadwalData = $jadwals->map(function ($j) {
            return [
                'id' => $j->id,
                'id_poli' => optional($j->dokter)->id_poli,
                'dokter' => optional($j->dokter)->nama,
                'hari' => $j->hari,
                'mulai' => substr($j->jam_mulai, 0, 5),
                'selesai' => substr($j->jam_selesai, 0, 5),
            ];
        })->values();
        $oldJadwal = (int) old('id_jadwal');
    @endphp

    <script>
        const jadwals = @json($jadwalData);
        const oldJadwal = @json($oldJadwal);

        const selectPoli = document.getElementById('id_poli');
        const selectJadwal = document.getElementById('id_jadwal');

        function isiJadwal() {
            const idPoli = parseInt(selectPoli.value);
            selectJadwal.innerHTML = '<option value="">-- Pilih Jadwal --</option>';

            if (!idPoli) return;

            const hasil = jadwals.filter((j) => j.id_poli === idPoli);

            if (hasil.length === 0) {
                selectJadwal.innerHTML = '<option value="">Belum ada jadwal untuk poli ini</option>';
                return;
            }

            hasil.forEach((j) => {
                const opt = document.createElement('option');
                opt.value = j.id;
                opt.textContent = `${j.dokter} - ${j.hari}, ${j.mulai} - ${j.selesai}`;
                if (j.id === oldJadwal) opt.selected = true;
                selectJadwal.appendChild(opt);
            });
        }

        selectPoli.addEventListener('change', isiJadwal);
        isiJadwal();
    </script>

</x-layouts.app>