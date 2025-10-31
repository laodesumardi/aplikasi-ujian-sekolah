@extends('layouts.teacher')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('guru.dashboard') }}" class="hover:underline">Guru</a>
        <span>/</span>
        <span class="text-white">Profil</span>
    </nav>
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

    <section aria-labelledby="profil-guru">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <h1 id="profil-guru" class="text-2xl lg:text-3xl font-bold text-gray-900">Profil Saya</h1>
        </div>

        <!-- Profile Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('guru.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <!-- Avatar Section -->
                <div class="bg-gradient-to-r from-primary/10 via-primary/5 to-white px-6 py-8 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-100">
                                @if($user->avatar)
                                    <img id="avatarPreview" src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <div id="avatarPreview" class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary to-primary/80 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <label for="avatar" class="absolute bottom-0 right-0 bg-primary text-white rounded-full p-2 shadow-lg cursor-pointer hover:bg-primary/90 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </label>
                            <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="hidden" onchange="previewAvatar(this)">
                            @if($user->avatar)
                                <button type="button" onclick="removeAvatar()" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 transition-colors" title="Hapus foto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @endif
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $user->name }}</h2>
                            <p class="text-gray-600 mb-2">{{ $user->email }}</p>
                            <p class="text-sm text-gray-500">
                                @if($user->role === 'guru')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Guru</span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Admin</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($user->role) }}</span>
                                @endif
                            </p>
                            <input type="hidden" name="remove_avatar" id="removeAvatar" value="0">
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="px-6 py-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kelas yang Diampu -->
                    <div>
                        <label for="guru_kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas yang Diampu</label>
                        <select name="guru_kelas[]" id="guru_kelas" multiple class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 @error('guru_kelas') border-red-500 @enderror" style="min-height: 100px;">
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" 
                                    @if($user->kelas)
                                        @php
                                            $userClasses = explode(',', $user->kelas);
                                            $userClasses = array_map('trim', $userClasses);
                                        @endphp
                                        {{ in_array($class->name, $userClasses) ? 'selected' : '' }}
                                    @endif
                                >{{ $class->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Tahan Ctrl/Cmd untuk memilih lebih dari satu kelas</p>
                        @error('guru_kelas')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('guru_kelas.*')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ubah Password</h3>
                        <p class="text-sm text-gray-600 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
                    </div>

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 @error('current_password') border-red-500 @enderror" placeholder="Masukkan password saat ini">
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password" id="password" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 @error('password') border-red-500 @enderror" placeholder="Masukkan password baru">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Konfirmasi password baru">
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
                    <a href="{{ route('guru.dashboard') }}" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors text-center">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        // Replace div with img
                        const img = document.createElement('img');
                        img.id = 'avatarPreview';
                        img.src = e.target.result;
                        img.alt = 'Avatar';
                        img.className = 'w-full h-full object-cover';
                        preview.parentNode.replaceChild(img, preview);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeAvatar() {
            if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
                document.getElementById('removeAvatar').value = '1';
                const preview = document.getElementById('avatarPreview');
                
                // Replace with default avatar div
                const div = document.createElement('div');
                div.id = 'avatarPreview';
                div.className = 'w-full h-full flex items-center justify-center bg-gradient-to-br from-primary to-primary/80 text-white';
                div.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                `;
                preview.parentNode.replaceChild(div, preview);
                
                // Reset file input
                document.getElementById('avatar').value = '';
            }
        }
    </script>
@endsection