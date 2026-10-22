<x-layouts.app title="Dashboard Pasien">

    @php
        $punyaAntrian = $antrianAktif !== null;
        $idJadwalAktif = $punyaAntrian ? $antrianAktif->id_jadwal : null;
    @endphp

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

        // Keadaan terakhir: { id_jadwal: nomor yang sedang dilayani }
        let dilayaniSekarang = @json($dilayani);

        function tampilkan(dilayani) {
            dilayaniSekarang = dilayani;

            // Tabel jadwal
            document.querySelectorAll('[data-dilayani]').forEach((el) => {
                const v = dilayani[el.dataset.dilayani];
                el.textContent = (v === undefined || v === null) ? '-' : v;
            });

            // Banner antrian aktif
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

        // Sinkronisasi penuh ke server (juga memeriksa apakah antrian pasien sudah selesai)
        async function sinkron() {
            if (document.hidden) return;
            try {
                const res = await fetch(urlLive, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();

                if (data.ada_antrian !== punyaAntrian) {
                    window.location.reload();
                    return;
                }
                tampilkan(data.dilayani || {});
            } catch (e) {
                // abaikan, dicoba lagi pada sinkron berikutnya
            }
        }

        tampilkan(dilayaniSekarang);

        // Script modul (Vite/Echo) baru berjalan sebelum DOMContentLoaded
        window.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                // Laravel Reverb (WebSocket): server yang mengirim, tidak perlu reload / polling cepat
                window.Echo.channel('antrian').listen('.antrian.diperbarui', (e) => {
                    const baru = Object.assign({}, dilayaniSekarang);
                    if (e.dilayani === null || e.dilayani === undefined) {
                        delete baru[e.id_jadwal];
                    } else {
                        baru[e.id_jadwal] = e.dilayani;
                    }
                    tampilkan(baru);
                    setStatus(true);

                    // Kalau jadwal ini milik antrian pasien, pastikan statusnya ikut disinkronkan
                    if (punyaAntrian && Number(e.id_jadwal) === Number(idJadwalAktif)) {
                        sinkron();
                    }
                });

                // Indikator koneksi WebSocket
                const koneksi = window.Echo.connector && window.Echo.connector.pusher
                    ? window.Echo.connector.pusher.connection : null;
                if (koneksi) {
                    koneksi.bind('connected', () => setStatus(true));
                    koneksi.bind('disconnected', () => setStatus(false));
                    koneksi.bind('unavailable', () => setStatus(false));
                }

                // Jaring pengaman: sinkron penuh tiap 30 detik (jika koneksi sempat terputus)
                setInterval(sinkron, 30000);
            } else {
                // Echo/Reverb belum terpasang: pakai pembaruan berkala sebagai cadangan
                setInterval(sinkron, 5000);
            }
        });
    </script>

</x-layouts.app>