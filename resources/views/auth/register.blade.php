<x-guest-layout>
    <div class="mb-6">
        <p class="clinic-kicker">Daftar pasien</p>
        <h1 class="mt-2 text-3xl font-black text-[#14342f]">Buat akses portal klinik.</h1>
        <p class="mt-2 text-sm leading-6 text-[#62756f]">Akun ini dipakai untuk booking antrean, melihat riwayat medis, dan pembayaran QRIS.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="mt-2 block" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama sesuai identitas" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" class="mt-2 block" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="no_hp" :value="__('Nomor HP / WhatsApp')" />
            <x-text-input id="no_hp" class="mt-2 block" type="tel" name="no_hp" :value="old('no_hp')" required autocomplete="tel" placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="password" :value="__('Kata Sandi')" />
                <x-text-input id="password" class="mt-2 block" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi')" />
                <x-text-input id="password_confirmation" class="mt-2 block" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <button type="submit" class="clinic-btn-primary w-full" id="btn-register-submit">
            Daftar Akun
        </button>

        <div class="border-t border-slate-100 pt-5 text-center text-sm font-semibold text-[#62756f]">
            Sudah punya akun?
            <a class="font-black text-[#ef7b2d] hover:text-[#c75f1d]" href="{{ route('login') }}">Masuk portal</a>
        </div>
    </form>
</x-guest-layout>
