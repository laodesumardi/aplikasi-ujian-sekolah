<x-guest-layout>
    <div class="mb-4 sm:mb-6 text-center">
        <h1 class="text-xl sm:text-2xl font-semibold text-primary mb-1 sm:mb-2">Masuk ke CBT Sekolah</h1>
        <p class="text-xs sm:text-sm text-gray-600 mt-1 px-2">Silakan login menggunakan akun yang telah diberikan.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3 sm:mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Email" class="text-sm sm:text-base" />
            <x-text-input id="email" class="block mt-1 sm:mt-2 w-full text-base sm:text-base" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@sekolah.sch.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 sm:mt-2 text-xs sm:text-sm" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi" class="text-sm sm:text-base" />
            <x-text-input id="password" class="block mt-1 sm:mt-2 w-full text-base sm:text-base" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 sm:mt-2 text-xs sm:text-sm" />
        </div>

        <!-- Remember Me & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary w-4 h-4 sm:w-5 sm:h-5" name="remember">
                <span class="ms-2 text-xs sm:text-sm text-gray-700">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs sm:text-sm text-primary hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary py-1" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3 sm:py-2.5 text-sm sm:text-base font-medium">
                Masuk
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4 sm:mt-6 text-center text-xs sm:text-sm text-gray-600 px-2">
        <p class="mb-1">Akun demo:</p>
        <p>
            <span class="font-medium break-all">siswa@example.com</span>
            <span class="mx-1">/</span>
            <span class="font-medium">password</span>
        </p>
    </div>
</x-guest-layout>
