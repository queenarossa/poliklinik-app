@php
    $mapStatus = [
        'belum_bayar'         => ['Belum Dibayar', 'bg-red-100 text-red-600'],
        'menunggu_verifikasi' => ['Menunggu Verifikasi', 'bg-amber-100 text-amber-600'],
        'lunas'               => ['Lunas', 'bg-green-100 text-green-600'],
    ];
    $infoStatus = $mapStatus[$status] ?? [$status, 'bg-slate-100 text-slate-600'];
@endphp
<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $infoStatus[1] }}">
    {{ $infoStatus[0] }}
</span>
