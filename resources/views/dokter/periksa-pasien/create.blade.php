<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa-pasien.index') }}" class="flex items-center justify-center w-9 h-9 
                  rounded-lg bg-slate-100 hover:bg-slate-200 
                  text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <h2 class="text-2xl font-bold text-slate-800">
            Periksa Pasien
        </h2>
    </div>

    {{-- Info Pasien --}}
    <div class="mb-6 px-5 py-4 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-600">
        <div class="flex flex-wrap gap-x-8 gap-y-1">
            <div>Pasien: <span class="font-semibold text-slate-800">{{ $daftarPoli->pasien->nama ?? '-' }}</span></div>
            <div>No Antrian: <span class="font-semibold text-slate-800">{{ $daftarPoli->no_antrian }}</span></div>
            <div>Keluhan: <span class="font-semibold text-slate-800">{{ $daftarPoli->keluhan }}</span></div>
        </div>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('periksa-pasien.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_daftar_poli" value="{{ $daftarPoli->id }}">

                {{-- Pilih Obat --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Pilih Obat <span class="text-red-500">*</span>
                    </label>
                    <select id="pilih-obat" class="w-full px-4 py-2 border-2 rounded-lg bg-white
                                   focus:border-primary focus:outline-none">
                        <option value="">-- Pilih Obat --</option>
                        @foreach ($obats as $obat)
                        <option value="{{ $obat->id }}">
                            {{ $obat->nama_obat }}{{ $obat->kemasan ? ' (' . $obat->kemasan . ')' : '' }}
                            - Rp {{ number_format($obat->harga, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    @error('obat')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    @error('obat.*')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Obat Terpilih --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Obat Terpilih
                    </label>
                    <ul id="daftar-obat" class="space-y-2"></ul>
                </div>

                {{-- Total Harga --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Total Harga
                    </label>
                    <div class="w-full px-4 py-3 rounded-lg bg-slate-100 text-sm space-y-1">
                        <div class="flex justify-between text-slate-500">
                            <span>Total obat</span>
                            <span id="total-obat">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Biaya jasa dokter</span>
                            <span>Rp {{ number_format($biayaJasa, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-slate-800 border-t border-slate-300 pt-1">
                            <span>Total</span>
                            <span id="total-harga">Rp {{ number_format($biayaJasa, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Catatan <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="catatan" rows="4" placeholder="Masukkan catatan..."
                        class="w-full px-4 py-2 border-2 rounded-lg
                               focus:border-primary focus:outline-none
                               @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary 
                               hover:bg-primary/90 text-white 
                               font-semibold text-sm transition">
                        <i class="fas fa-save mr-1"></i>
                        Simpan
                    </button>

                    <a href="{{ route('periksa-pasien.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 
                              hover:bg-slate-200 text-slate-600 
                              font-semibold text-sm transition">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

    @php
        $obatData = $obats->map(function ($o) {
            return [
                'id' => $o->id,
                'nama' => $o->nama_obat,
                'kemasan' => $o->kemasan,
                'harga' => (int) $o->harga,
            ];
        })->values();
        $obatLama = array_map('intval', old('obat', []));
    @endphp

    <script>
        const dataObat = @json($obatData);

        const biayaJasa = {{ (int) $biayaJasa }};

        // Dipulihkan jika validasi gagal
        let terpilih = @json($obatLama);

        const select = document.getElementById('pilih-obat');
        const daftar = document.getElementById('daftar-obat');
        const totalObatEl = document.getElementById('total-obat');
        const totalEl = document.getElementById('total-harga');

        const rupiah = (angka) => 'Rp ' + angka.toLocaleString('id-ID');
        const esc = (t) => String(t ?? '').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));

        function render() {
            daftar.innerHTML = '';
            let totalObat = 0;

            if (terpilih.length === 0) {
                daftar.innerHTML = '<li class="text-sm text-slate-400">Belum ada obat dipilih</li>';
            }

            terpilih.forEach((id) => {
                const obat = dataObat.find((o) => o.id === id);
                if (!obat) return;
                totalObat += obat.harga;

                const li = document.createElement('li');
                li.className = 'flex items-center justify-between px-4 py-2 rounded-lg bg-slate-50 border border-slate-200 text-sm';
                li.innerHTML = `
                    <span class="text-slate-700">
                        <span class="font-semibold">${esc(obat.nama)}</span>
                        ${obat.kemasan ? `<span class="text-slate-400">(${esc(obat.kemasan)})</span>` : ''}
                        <span class="ml-2 text-slate-500">${rupiah(obat.harga)}</span>
                    </span>
                    <button type="button" data-id="${obat.id}"
                        class="btn-hapus px-3 py-1 rounded-lg bg-red-500 hover:bg-red-600 text-white text-xs font-semibold">
                        <i class="fas fa-trash text-xs"></i> Hapus
                    </button>
                    <input type="hidden" name="obat[]" value="${obat.id}">
                `;
                daftar.appendChild(li);
            });

            totalObatEl.textContent = rupiah(totalObat);
            totalEl.textContent = rupiah(totalObat + biayaJasa);
        }

        select.addEventListener('change', () => {
            const id = parseInt(select.value);
            if (id && !terpilih.includes(id)) {
                terpilih.push(id);
                render();
            }
            select.value = '';
        });

        daftar.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-hapus');
            if (!btn) return;
            const id = parseInt(btn.dataset.id);
            terpilih = terpilih.filter((x) => x !== id);
            render();
        });

        render();
    </script>

</x-layouts.app>