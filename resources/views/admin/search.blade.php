@extends('layouts.admin')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:underline">Admin</a>
        <span>/</span>
        <span class="text-white">Hasil Pencarian</span>
    </nav>
@endsection

@section('content')
    <section aria-labelledby="hasil-pencarian">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <h1 id="hasil-pencarian" class="text-2xl lg:text-3xl font-bold text-gray-900">Hasil Pencarian</h1>
            <p class="text-sm text-gray-600">Kata kunci: <strong>"{{ $query }}"</strong></p>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
            <form method="GET" action="{{ route('admin.search') }}" class="flex items-center gap-4">
                <div class="relative flex-1 sm:max-w-md">
                    <input type="search" name="q" value="{{ $query }}" placeholder="Cari pengguna, mata pelajaran, kelas..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/></svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors">
                    Cari
                </button>
            </form>
        </div>

        <!-- Results -->
        <div class="space-y-6">
            <!-- Users Results -->
            @if($results['users']->count() > 0)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 110-8 4 4 0 010 8z"/></svg>
                            Pengguna ({{ $results['users']->count() }})
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($results['users'] as $user)
                            <a href="{{ route('admin.users', ['search' => $query]) }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $user->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
                                        <div class="flex items-center gap-2 mt-2">
                                            @php
                                                $roleColors = ['admin' => 'bg-red-100 text-red-800', 'guru' => 'bg-primary/10 text-primary', 'siswa' => 'bg-green-100 text-green-800'];
                                                $color = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                            @if($user->kelas)
                                                <span class="text-xs text-gray-500">{{ $user->kelas }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <a href="{{ route('admin.users', ['search' => $query]) }}" class="text-sm text-primary hover:text-primary/80 font-medium">
                            Lihat semua hasil pengguna →
                        </a>
                    </div>
                </div>
            @endif

            <!-- Subjects Results -->
            @if($results['subjects']->count() > 0)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h18M5 9h14M7 13h10M9 17h6"/></svg>
                            Mata Pelajaran ({{ $results['subjects']->count() }})
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($results['subjects'] as $subject)
                            <a href="{{ route('admin.subjects', ['search' => $query]) }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $subject->name }}</h3>
                                        @if($subject->code)
                                            <p class="text-sm text-gray-600 mt-1">Kode: {{ $subject->code }}</p>
                                        @endif
                                        @if($subject->description)
                                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($subject->description, 100) }}</p>
                                        @endif
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <a href="{{ route('admin.subjects', ['search' => $query]) }}" class="text-sm text-primary hover:text-primary/80 font-medium">
                            Lihat semua hasil mata pelajaran →
                        </a>
                    </div>
                </div>
            @endif

            <!-- Classes Results -->
            @if($results['classes']->count() > 0)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z"/></svg>
                            Kelas ({{ $results['classes']->count() }})
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($results['classes'] as $class)
                            <a href="{{ route('admin.classes', ['search' => $query]) }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $class->name }}</h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($class->level)
                                                <span class="text-xs text-gray-600">Tingkat: {{ $class->level }}</span>
                                            @endif
                                            @if($class->program)
                                                <span class="text-xs text-gray-600">Program: {{ $class->program }}</span>
                                            @endif
                                            @if($class->code)
                                                <span class="text-xs text-gray-600">Kode: {{ $class->code }}</span>
                                            @endif
                                        </div>
                                        @if($class->capacity)
                                            <p class="text-sm text-gray-500 mt-1">Kapasitas: {{ $class->capacity }} siswa</p>
                                        @endif
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <a href="{{ route('admin.classes', ['search' => $query]) }}" class="text-sm text-primary hover:text-primary/80 font-medium">
                            Lihat semua hasil kelas →
                        </a>
                    </div>
                </div>
            @endif

            <!-- No Results -->
            @if($results['users']->count() === 0 && $results['subjects']->count() === 0 && $results['classes']->count() === 0)
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada hasil ditemukan</h3>
                    <p class="text-sm text-gray-600 mb-4">Tidak ada pengguna, mata pelajaran, atau kelas yang cocok dengan kata kunci "<strong>{{ $query }}</strong>".</p>
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection



