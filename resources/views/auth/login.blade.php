<x-guest-layout>
    <div class="mb-6">
        <p class="clinic-kicker">Masuk portal</p>
        <h1 class="mt-2 text-3xl font-black text-[#14342f]">Login Klinik Ar-Ridlo</h1>
        <p class="mt-2 text-sm leading-6 text-[#62756f]">Masuk sebagai pasien atau admin. Sistem akan membuka portal sesuai role akun.</p>
    </div>

    <x-auth-session-status class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm font-semibold text-emerald-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-4">
                <x-input-label for="password" :value="__('Kata Sandi')" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-[#ef7b2d] transition hover:text-[#c75f1d]" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="mt-2 block" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-[#46665f]">
            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-[#7ba891] shadow-sm focus:ring-[#7ba891]" name="remember">
            Ingat sesi saya
        </label>

        <button type="submit" class="clinic-btn-primary w-full">
            Masuk
        </button>

        <div class="border-t border-slate-100 pt-5 text-center text-sm font-semibold text-[#62756f]">
            Akun baru hanya untuk pasien.
            <a href="{{ route('register') }}" class="font-black text-[#ef7b2d] hover:text-[#c75f1d]">Daftar pasien</a>
        </div>
    </form>
</x-guest-layout>
