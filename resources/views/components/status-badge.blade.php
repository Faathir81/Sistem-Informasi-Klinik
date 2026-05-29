@props([
    'type' => 'generic',
    'value' => null,
    'dot' => true,
    'dotId' => null,
    'contrast' => false,
])

@php
    $state = (string) ($value ?? 'Belum Mengajukan');

    $badgeClass = match ($type) {
        'antrean' => match ($state) {
            \App\Enums\AntreanStatus::Menunggu->value => 'clinic-badge-warning',
            \App\Enums\AntreanStatus::Dipanggil->value => 'clinic-badge-info',
            \App\Enums\AntreanStatus::Selesai->value => 'clinic-badge-success',
            \App\Enums\AntreanStatus::Batal->value => 'clinic-badge-muted',
            default => 'clinic-badge-muted',
        },
        'pengajuan' => match ($state) {
            \App\Enums\PengajuanPasienStatus::MenungguPembayaran->value => 'clinic-badge-warning',
            \App\Enums\PengajuanPasienStatus::Menunggu->value => 'clinic-badge-warning',
            \App\Enums\PengajuanPasienStatus::Disetujui->value => 'clinic-badge-success',
            \App\Enums\PengajuanPasienStatus::PembayaranGagal->value => 'clinic-badge-danger',
            \App\Enums\PengajuanPasienStatus::Ditolak->value => 'clinic-badge-danger',
            default => 'clinic-badge-muted',
        },
        'payment' => $state === \App\Enums\PaymentStatus::Lunas->value ? 'clinic-badge-success' : 'clinic-badge-warning',
        'transaction' => match ($state) {
            \App\Enums\TransaksiStatus::Settlement->value => 'clinic-badge-success',
            \App\Enums\TransaksiStatus::Pending->value => 'clinic-badge-warning',
            \App\Enums\TransaksiStatus::Cancel->value => 'clinic-badge-danger',
            default => 'clinic-badge-muted',
        },
        'pickup' => $state === 'Sudah_Diambil' ? 'clinic-badge-success' : 'clinic-badge-warning',
        default => 'clinic-badge-muted',
    };

    $dotClass = match ($badgeClass) {
        'clinic-badge-success' => 'bg-emerald-500',
        'clinic-badge-warning' => 'bg-amber-500',
        'clinic-badge-info' => 'bg-sky-500',
        'clinic-badge-danger' => 'bg-red-500',
        default => 'bg-slate-400',
    };

    $label = str_replace('_', ' ', $state);
    $contrastClass = $contrast ? ' bg-white/10 text-white border-white/20' : '';
@endphp

<span {{ $attributes->merge(['class' => $badgeClass.$contrastClass]) }}>
    @if ($dot)
        <span @if ($dotId) id="{{ $dotId }}" @endif class="h-2 w-2 rounded-full {{ $dotClass }}"></span>
    @endif
    @if ($slot->isEmpty())
        {{ $label }}
    @else
        {{ $slot }}
    @endif
</span>
