@php
    $isAdmin = Auth::user()->role === 'admin';
    $portalHome = $isAdmin ? url('/admin') : route('pasien.dashboard');
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/70 bg-white/80 backdrop-blur-xl">
    <div class="clinic-section">
        <div class="flex h-[72px] items-center justify-between gap-4 py-3">
            <div class="flex items-center gap-8">
                <a href="{{ $portalHome }}" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-md bg-[#14342f] text-white shadow-sm">
                        <x-application-logo class="h-8 w-8" />
                    </span>
                    <span class="hidden sm:block">
                        <span class="block text-sm font-black leading-5 text-[#14342f]">Klinik Ar-Ridlo</span>
                        <span class="block text-xs font-semibold text-[#62756f]">{{ $isAdmin ? 'Panel Admin' : 'Portal Pasien' }}</span>
                    </span>
                </a>

                @unless($isAdmin)
                    <div class="hidden items-center gap-1 lg:flex">
                        <x-nav-link :href="route('pasien.dashboard')" :active="request()->routeIs('pasien.dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="route('pasien.antrean.index')" :active="request()->routeIs('pasien.antrean.*')">
                            Antrean
                        </x-nav-link>
                        <x-nav-link :href="route('pasien.riwayat.index')" :active="request()->routeIs('pasien.riwayat.*')">
                            Riwayat
                        </x-nav-link>
                        <x-nav-link :href="route('pasien.pembayaran.index')" :active="request()->routeIs('pasien.pembayaran.*')">
                            Pembayaran
                        </x-nav-link>
                    </div>
                @endunless
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                @if($isAdmin)
                    <a href="/admin" class="clinic-btn-primary min-h-10 px-4 py-2">
                        Panel Admin
                    </a>
                @else
                    <a href="{{ route('pasien.antrean.create') }}" class="clinic-btn-primary min-h-10 px-4 py-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                        </svg>
                        Booking
                    </a>
                @endif

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="clinic-focus-ring inline-flex items-center gap-3 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-[#14342f] shadow-sm transition hover:bg-[#f3faf6]">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md bg-[#e1f1e8] text-sm font-black text-[#14342f]">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden max-w-36 truncate xl:inline">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 text-[#62756f]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="truncate text-sm font-bold text-[#14342f]">{{ Auth::user()->name }}</p>
                            <p class="truncate text-xs font-medium text-[#62756f]">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil dan keamanan
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = ! open" class="clinic-focus-ring inline-flex h-11 w-11 items-center justify-center rounded-md border border-slate-200 bg-white text-[#14342f] sm:hidden" aria-label="Buka menu">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-100 bg-white sm:hidden">
        @unless($isAdmin)
            <div class="space-y-1 py-3">
                <x-responsive-nav-link :href="route('pasien.dashboard')" :active="request()->routeIs('pasien.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pasien.antrean.index')" :active="request()->routeIs('pasien.antrean.*')">
                    Antrean
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pasien.riwayat.index')" :active="request()->routeIs('pasien.riwayat.*')">
                    Riwayat Medis
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pasien.pembayaran.index')" :active="request()->routeIs('pasien.pembayaran.*')">
                    Pembayaran
                </x-responsive-nav-link>
            </div>
        @endunless

        <div class="border-t border-slate-100 px-4 py-4">
            <div class="mb-3">
                <div class="font-bold text-[#14342f]">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-[#62756f]">{{ Auth::user()->email }}</div>
            </div>

            <div class="grid gap-2">
                @if($isAdmin)
                    <a href="/admin" class="clinic-btn-primary w-full">
                        Panel Admin
                    </a>
                @else
                    <a href="{{ route('pasien.antrean.create') }}" class="clinic-btn-primary w-full">
                        Booking Antrean
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}" class="clinic-btn-secondary w-full">
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="clinic-btn-quiet w-full justify-center">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
