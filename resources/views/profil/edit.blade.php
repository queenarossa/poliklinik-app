<x-layouts.app title="Profil">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Profil</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Informasi Akun --}}
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-6">
                <h3 class="font-bold text-slate-800 mb-4">Informasi Akun</h3>

                <div class="flex items-center gap-4 mb-5">
                    <div class="w-14 h-14 rounded-full bg-primary text-primary-content flex items-center justify-center">
                        <span class="text-xl font-semibold">{{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $user->nama }}</p>
                        <span class="inline-block mt-1 px-3 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-600">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>

                <div class="text-sm space-y-2 text-slate-600">
                    <div class="flex justify-between">
                        <span>Email</span>
                        <span class="font-semibold text-slate-800">{{ $user->email }}</span>
                    </div>
                    @if ($user->role === 'pasien')
                    <div class="flex justify-between">
                        <span>No. Rekam Medis</span>
                        <span class="font-semibold text-slate-800">{{ $user->no_rm ?? '-' }}</span>
                    </div>
                    @endif
                    @if (! empty($user->no_hp))
                    <div class="flex justify-between">
                        <span>No. HP</span>
                        <span class="font-semibold text-slate-800">{{ $user->no_hp }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ganti Password --}}
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-6">
                <h3 class="font-bold text-slate-800 mb-4">
                    <i class="fas fa-lock text-primary mr-1"></i>
                    Ganti Password
                </h3>

                <form action="{{ route($user->role . '.profil.password') }}" method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')

                    {{-- Password Lama --}}
                    <div class="mb-4">
                        <label for="password_lama" class="block text-xs font-semibold text-slate-700 mb-1">
                            Password Lama <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_lama" id="password_lama" autocomplete="current-password"
                                class="w-full px-3 py-2 pr-10 text-sm border rounded-lg focus:border-primary focus:outline-none
                                       @error('password_lama') border-red-500 @enderror">
                            <button type="button" data-toggle="password_lama"
                                class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600" tabindex="-1">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password_lama')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div class="mb-4">
                        <label for="password_baru" class="block text-xs font-semibold text-slate-700 mb-1">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_baru" id="password_baru" autocomplete="new-password"
                                class="w-full px-3 py-2 pr-10 text-sm border rounded-lg focus:border-primary focus:outline-none
                                       @error('password_baru') border-red-500 @enderror">
                            <button type="button" data-toggle="password_baru"
                                class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600" tabindex="-1">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Minimal 8 karakter dan berbeda dari password lama.</p>
                        @error('password_baru')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi --}}
                    <div class="mb-6">
                        <label for="konfirmasi_password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="konfirmasi_password" id="konfirmasi_password" autocomplete="new-password"
                                class="w-full px-3 py-2 pr-10 text-sm border rounded-lg focus:border-primary focus:outline-none
                                       @error('konfirmasi_password') border-red-500 @enderror">
                            <button type="button" data-toggle="konfirmasi_password"
                                class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600" tabindex="-1">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('konfirmasi_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2 rounded-lg bg-primary hover:bg-primary/90
                                   text-white text-sm font-semibold transition">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        // Tombol mata: tampilkan / sembunyikan password
        document.querySelectorAll('[data-toggle]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.toggle);
                const icon = btn.querySelector('i');
                const tampil = input.type === 'password';
                input.type = tampil ? 'text' : 'password';
                icon.className = tampil ? 'fas fa-eye-slash text-sm' : 'fas fa-eye text-sm';
            });
        });
    </script>

</x-layouts.app>
