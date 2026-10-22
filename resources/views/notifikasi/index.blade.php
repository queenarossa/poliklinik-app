<x-layouts.app title="Notifikasi">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Notifikasi</h2>

        @if (auth()->user()->unreadNotifications()->count() > 0)
        <form action="{{ route(auth()->user()->role . '.notifikasi.baca-semua') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm bg-primary hover:bg-primary/90 text-white border-none rounded-lg px-4">
                <i class="fas fa-check-double"></i>
                Tandai semua dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Daftar Notifikasi --}}
    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">

            @forelse ($notifikasis as $notif)
            <div class="flex items-start gap-4 px-6 py-4 border-b border-slate-100 last:border-b-0
                        {{ $notif->read_at ? 'bg-white' : 'bg-indigo-50' }}">

                <div class="mt-1 w-9 h-9 rounded-full flex items-center justify-center shrink-0
                            {{ $notif->read_at ? 'bg-slate-100 text-slate-400' : 'bg-indigo-100 text-indigo-600' }}">
                    <i class="fas fa-bell text-sm"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm {{ $notif->read_at ? 'font-medium text-slate-600' : 'font-bold text-slate-800' }}">
                            {{ $notif->data['judul'] ?? 'Notifikasi' }}
                        </p>
                        @if (! $notif->read_at)
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-500 text-white">Baru</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $notif->data['pesan'] ?? '' }}</p>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ $notif->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
                        &middot; {{ $notif->created_at->locale('id')->diffForHumans() }}
                    </p>
                </div>

                @if (! $notif->read_at)
                <form action="{{ route(auth()->user()->role . '.notifikasi.baca', $notif->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-ghost text-primary">
                        <i class="fas fa-check"></i>
                        Tandai dibaca
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div class="text-center py-16 text-slate-400">
                <i class="fas fa-bell-slash text-3xl mb-3 block"></i>
                Belum ada notifikasi
            </div>
            @endforelse

        </div>
    </div>

    <div class="mt-4">
        {{ $notifikasis->links() }}
    </div>

</x-layouts.app>