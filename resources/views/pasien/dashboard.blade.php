<x-layouts.app title="Dashboard Pasien">

    @php
        $punyaAntrian = $antrianAktif !== null;
        $idJadwalAktif = $punyaAntrian ? $antrianAktif->id_jadwal : null;

        $jam = (int) now()->format('G');
        $sapaan = match (true) {
            $jam < 11 => 'Selamat pagi',
            $jam < 15 => 'Selamat siang',
            $jam < 18 => 'Selamat sore',
            default   => 'Selamat malam',
        };
        $tanggal = now()->locale('id')->translatedFormat('l, d F Y');
    @endphp

    {{-- Selamat Datang --}}
    <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm p-6 mb-6">
        <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-indigo-50"></div>
        <div class="absolute right-16 -bottom-12 w-28 h-28 rounded-full bg-blue-50"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 shrink-0 rounded-full bg-gradient-to-br from-indigo-600 to-blue-500 text-white
                            flex items-center justify-center text-xl font-bold shadow">
                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 flex items-center gap-1.5">
                        <i class="fas fa-calendar-day"></i> {{ $tanggal }} &middot; {{ $sapaan }}
                    </p>
                    <h2 class="text-2xl font-extrabold text-slate-800 leading-tight">
                        Selamat datang, <span class="text-indigo-600">{{ $user->nama }}</span>
                    </h2>
                    <p class="text-sm text-slate-400">Semoga lekas sehat. Pantau antrian dan jadwal poliklinik Anda di sini.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 rounded-xl bg-slate-50 border border-slate-200 px-5 py-3 self-start md:self-auto">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-id-card-clip"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">No. Rekam Medis</p>
                    <p class="text-lg font-bold text-slate-800 tracking-wide">{{ $user->no_rm }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Banner Antrian Aktif --}}
    @if ($punyaAntrian)
    @php
        $jadwalAktif = $antrianAktif->jadwalPeriksa;
    @endphp
    <div class="rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-500 text-white p-6 mb-8 shadow-md">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            {{-- Info --}}
            <div>
                <p class="text-xs font-bold tracking-widest uppercase mb-4">Antrian Aktif Anda</p>

                <div class="space-y-3">
                    <div>
                        <p class="text-[11px] text-indigo-100">Poliklinik</p>
                        <p class="text-lg font-bold">{{ $jadwalAktif->dokter->poli->nama_poli ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-indigo-100">Dokter</p>
                        <p class="font-semibold">{{ $jadwalAktif->dokter->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-indigo-100">Jadwal Periksa</p>
                        <p class="font-semibold">
                            {{ $jadwalAktif->hari }}
                            ({{ \Carbon\Carbon::parse($jadwalAktif->jam_mulai)->format('H:i') }}
                            - {{ \Carbon\Carbon::parse($jadwalAktif->jam_selesai)->format('H:i') }})
                        </p>
                    </div>
                </div>
            </div>

            {{-- Nomor --}}
            <div class="flex gap-4">
                <div class="w-32 rounded-xl bg-white/20 text-center py-4 px-3">
                    <p class="text-[11px] text-indigo-100 mb-1">Nomor Anda</p>
                    <p class="text-4xl font-bold">{{ $antrianAktif->no_antrian }}</p>
                </div>

                <div class="w-36 rounded-xl bg-white text-indigo-700 text-center py-4 px-3 shadow">
                    <p class="text-[10px] font-bold tracking-wider uppercase text-slate-500 mb-1">Sedang Dilayani</p>
                    <p id="banner-dilayani" class="text-4xl font-bold">{{ $dilayani[$idJadwalAktif] ?? '-' }}</p>
                    <p class="mt-1 text-[10px] text-slate-400 flex items-center justify-center gap-1">
                        <span id="live-dot" class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        <span id="live-text">Live update</span>
                    </p>
                </div>
            </div>

        </div>

        <p id="banner-giliran" class="hidden mt-5 text-sm font-semibold bg-white/20 rounded-lg px-4 py-2">
            <i class="fas fa-bell mr-1"></i> Sekarang giliran Anda, silakan menuju ruang periksa.
        </p>
    </div>
    @else
    {{-- Tidak ada antrian aktif --}}
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 mb-8 text-center">
        <i class="fas fa-ticket text-3xl text-slate-300 mb-3 block"></i>
        <p class="font-semibold text-slate-700">Anda belum memiliki antrian aktif</p>
        <p class="text-sm text-slate-400 mb-4">Daftar ke poli untuk mendapatkan nomor antrian.</p>
        <a href="{{ route('pasien.daftar') }}" class="inline-block px-5 py-2 rounded-lg bg-primary hover:bg-primary/90
                  text-white text-sm font-semibold transition">
            Daftar Poli
        </a>
    </div>
    @endif

    {{-- Jadwal Poliklinik dan Dokter --}}
    <h3 class="text-lg font-bold text-slate-800 mb-3">Jadwal Poliklinik</h3>

    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Hari</th>
                            <th class="px-6 py-4">Jam Periksa</th>
                            <th class="px-6 py-4 text-center">Nomor Dilayani</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($jadwals as $jadwal)
                        <tr class="hover:bg-slate-50 transition {{ $jadwal->id === $idJadwalAktif ? 'bg-indigo-50' : '' }}">
                            <td class="px-6 py-4 text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $jadwal->dokter->poli->nama_poli ?? '-' }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $jadwal->dokter->nama ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $jadwal->hari }}</td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span data-dilayani="{{ $jadwal->id }}"
                                    class="inline-block min-w-8 px-3 py-1 text-xs font-bold rounded-full bg-indigo-100 text-indigo-700">
                                    {{ $dilayani[$jadwal->id] ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Belum ada jadwal poliklinik
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const urlLive = @json(route('pasien.antrian.live'));
        const punyaAntrian = @json($punyaAntrian);
        const idJadwalAktif = @json($idJadwalAktif);
        const nomorSaya = @json($punyaAntrian ? $antrianAktif->no_antrian : null);
        const INTERVAL_MS = 5000;

        function tampilkan(dilayani) {
            // Tabel jadwal
            document.querySelectorAll('[data-dilayani]').forEach((el) => {
                const v = dilayani[el.dataset.dilayani];
                el.textContent = (v === undefined || v === null) ? '-' : v;
            });

            // Banner
            if (punyaAntrian) {
                const v = dilayani[idJadwalAktif];
                const bannerEl = document.getElementById('banner-dilayani');
                if (bannerEl) bannerEl.textContent = (v === undefined || v === null) ? '-' : v;

                const giliran = document.getElementById('banner-giliran');
                if (giliran) giliran.classList.toggle('hidden', Number(v) !== Number(nomorSaya));
            }
        }

        function setStatus(ok) {
            const dot = document.getElementById('live-dot');
            const text = document.getElementById('live-text');
            if (!dot || !text) return;
            dot.className = 'inline-block w-1.5 h-1.5 rounded-full ' + (ok ? 'bg-green-500 animate-pulse' : 'bg-red-500');
            text.textContent = ok ? 'Live update' : 'Terputus';
        }

        async function perbarui() {
            if (document.hidden) return;
            try {
                const res = await fetch(urlLive, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();

                // Status antrian berubah (mis. pasien sudah diperiksa) -> muat ulang halaman
                if (data.ada_antrian !== punyaAntrian) {
                    window.location.reload();
                    return;
                }

                tampilkan(data.dilayani || {});
                setStatus(true);
            } catch (e) {
                setStatus(false);
            }
        }

        tampilkan(@json($dilayani));
        setInterval(perbarui, INTERVAL_MS);
    </script>

</x-layouts.app>