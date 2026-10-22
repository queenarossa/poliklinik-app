<header class="bg-base-100 border-b border-base-300 h-16 flex items-center px-6 gap-4 sticky top-0 z-30 shadow-sm">

    {{-- Mobile Hamburger --}}
    <button onclick="toggleSidebar()" class="btn btn-square btn-ghost lg:hidden">
        <i data-lucide="menu" class="w-5 h-5"></i>
    </button>

    {{-- Breadcrumb --}}
    <div class="flex-1">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-base-content/50">Poliklinik</span>
            <i data-lucide="chevron-right" class="w-4 h-4 text-base-content/30"></i>
            <span class="font-semibold text-base-content">
                {{ $title ?? 'Dashboard' }}
            </span>
        </div>
    </div>

    {{-- Fullscreen --}}
    <button onclick="toggleFullscreen()" class="btn btn-square btn-ghost">
        <i id="fsIcon" class="fas fa-expand w-5 h-5"></i>
    </button>

    {{-- Notifikasi --}}
    @php
        $userNotif = auth()->user();
        $roleNotif = $userNotif->role;
        $jumlahBelumDibaca = $userNotif->unreadNotifications()->count();
        $notifTerbaru = $userNotif->notifications()->latest()->take(5)->get();
    @endphp

    <div class="relative" id="notifWrapper">
        <button type="button" onclick="toggleNotif(event)" class="btn btn-square btn-ghost relative" aria-label="Notifikasi">
            <i class="fas fa-bell w-5 h-5"></i>
            @if ($jumlahBelumDibaca > 0)
            <span class="absolute top-1 right-1 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white
                         text-[10px] font-bold flex items-center justify-center">
                {{ $jumlahBelumDibaca > 99 ? '99+' : $jumlahBelumDibaca }}
            </span>
            @endif
        </button>

        <div id="notifPanel" class="hidden absolute right-0 mt-2 w-80 max-w-[90vw] bg-white border border-slate-200
                                    rounded-xl shadow-lg z-50 overflow-hidden">

            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                <span class="text-sm font-bold text-slate-800">Notifikasi</span>
                @if ($jumlahBelumDibaca > 0)
                <form action="{{ route($roleNotif . '.notifikasi.baca-semua') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-primary hover:underline">
                        Tandai semua dibaca
                    </button>
                </form>
                @endif
            </div>

            <div class="max-h-80 overflow-y-auto">
                @forelse ($notifTerbaru as $notif)
                <div class="px-4 py-3 border-b border-slate-50 {{ $notif->read_at ? 'bg-white' : 'bg-indigo-50' }}">
                    <div class="flex items-start gap-2">
                        @if (! $notif->read_at)
                        <span class="mt-1.5 w-2 h-2 rounded-full bg-indigo-500 shrink-0"></span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-xs {{ $notif->read_at ? 'font-medium text-slate-600' : 'font-bold text-slate-800' }}">
                                {{ $notif->data['judul'] ?? 'Notifikasi' }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $notif->data['pesan'] ?? '' }}</p>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-[10px] text-slate-400">
                                    {{ $notif->created_at->locale('id')->diffForHumans() }}
                                </span>
                                @if (! $notif->read_at)
                                <form action="{{ route($roleNotif . '.notifikasi.baca', $notif->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-semibold text-primary hover:underline">
                                        Tandai dibaca
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-sm text-slate-400">
                    <i class="fas fa-bell-slash text-2xl mb-2 block"></i>
                    Belum ada notifikasi
                </div>
                @endforelse
            </div>

            <a href="{{ route($roleNotif . '.notifikasi.index') }}"
                class="block text-center text-xs font-semibold text-primary py-3 border-t border-slate-100 hover:bg-slate-50">
                Lihat semua notifikasi
            </a>
        </div>
    </div>

    {{-- User Info (klik untuk membuka Profil) --}}
    <a href="{{ route(auth()->user()->role . '.profil.edit') }}" class="flex items-center gap-3 hover:opacity-80 transition" title="Profil &amp; ganti password">

        <div class="text-right hidden sm:block">
            <div class="text-sm font-semibold leading-tight">
                {{ auth()->user()->nama ?? 'Pengguna' }}
            </div>
            <div class="text-xs text-base-content/50 leading-tight">
                {{ auth()->user()->role ?? 'Admin Sistem' }}
            </div>
        </div>

        <div class="avatar">
            <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                <span class="text-sm font-semibold leading-none">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                </span>
            </div>
        </div>
    </a>

</header>

<script>
    function toggleFullscreen() {
    const icon = document.getElementById('fsIcon');

    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        icon.classList.remove('fa-expand');
        icon.classList.add('fa-compress');
    } else {
        document.exitFullscreen();
        icon.classList.remove('fa-compress');
        icon.classList.add('fa-expand');
    }
}
</script>

<script>
    function toggleNotif(e) {
        e.stopPropagation();
        document.getElementById('notifPanel').classList.toggle('hidden');
    }

    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('notifWrapper');
        const panel = document.getElementById('notifPanel');
        if (wrapper && panel && !wrapper.contains(e.target)) {
            panel.classList.add('hidden');
        }
    });
</script>