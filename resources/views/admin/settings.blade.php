@extends('layouts.admin')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:underline">Admin</a>
        <span>/</span>
        <span class="text-white">Pengaturan</span>
    </nav>
@endsection

@section('header-right')
    <button type="submit" form="settingsForm" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span>Simpan</span>
    </button>
@endsection

@section('content')
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <section aria-labelledby="pengaturan-aplikasi">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <h1 id="pengaturan-aplikasi" class="text-2xl lg:text-3xl font-bold text-gray-900">Pengaturan Aplikasi Sistem</h1>
        </div>

        <form id="settingsForm" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 border-b pb-3">Parameter Global</h2>

                <!-- Nama Aplikasi -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Aplikasi <span class="text-red-500">*</span></label>
                    <input type="text" name="app_name" value="{{ $appName }}" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Masukkan nama aplikasi">
                    <p class="text-xs text-gray-500 mt-1">Nama aplikasi akan ditampilkan di semua halaman.</p>
                </div>

                <!-- Logo Aplikasi -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Aplikasi</label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex-shrink-0">
                            @if($logoPath)
                                @php
                                    $publicLogoUrl = ltrim(Storage::url($logoPath), '/');
                                    $secureLogoUrl = (request()->secure() || config('app.env') === 'production')
                                        ? secure_asset($publicLogoUrl)
                                        : asset($publicLogoUrl);
                                @endphp
                                <img src="{{ $secureLogoUrl }}" alt="Current Logo" id="logoPreview" class="w-32 h-32 object-contain border rounded-lg p-2 bg-gray-50">
                            @else
                                <div class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50" id="logoPreview">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="logo" id="logoInput" accept="image/jpeg,image/jpg,image/png,image/svg+xml" class="block w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" onchange="previewLogo(this)">
                            <p class="text-xs text-gray-500 mt-2">Format: PNG, JPG, SVG. Maks 2MB.</p>
                            @if($logoPath)
                                <button type="button" onclick="removeLogo()" class="mt-2 text-sm text-red-600 hover:text-red-800">Hapus Logo Saat Ini</button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tahun Ajaran -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" value="{{ $tahunAjaran }}" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="2024/2025">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Batas Waktu Sesi Login -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Batas Waktu Sesi Login (menit)</label>
                        <input type="number" name="session_timeout" value="{{ $sessionTimeout }}" min="1" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>

                    <!-- Toggle Maintenance Mode -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Mode</label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer" {{ $maintenance ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary relative transition-colors">
                                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></span>
                            </div>
                            <span class="ml-3 text-sm">Aktifkan Maintenance</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors shadow-md font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    <script>
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('logoPreview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview" class="w-32 h-32 object-contain border rounded-lg p-2 bg-gray-50">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeLogo() {
            if (confirm('Apakah Anda yakin ingin menghapus logo saat ini?')) {
                document.getElementById('logoInput').value = '';
                const preview = document.getElementById('logoPreview');
                preview.innerHTML = `
                    <div class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                `;
            }
        }
    </script>
@endsection