@extends('layouts.teacher')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('guru.dashboard') }}" class="hover:underline">Guru</a>
        <span>/</span>
        <span class="text-white">Dashboard</span>
    </nav>
@endsection

@section('header-right')
    <a href="{{ route('guru.profile') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4 0-7 2-7 4v2h14v-2c0-2-3-4-7-4z"/></svg>
        <span>Profil</span>
    </a>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Welcome Section -->
        <div class="mb-6 lg:mb-8">
            <div class="bg-gradient-to-r from-primary via-primary/95 to-primary/90 p-6 md:p-8 lg:p-10 text-white rounded-xl lg:rounded-2xl shadow-lg">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 lg:gap-6">
                    <div class="flex-1">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-2 lg:mb-3 leading-tight">Selamat Datang, {{ Auth::user()->name }}!</h1>
                        <p class="text-base md:text-lg lg:text-xl text-white/90 leading-relaxed">Berikut adalah ringkasan statistik dan aktivitas Anda sebagai Guru.</p>
                    </div>
                    <div class="hidden xl:block flex-shrink-0">
                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-10 h-10 lg:w-12 lg:h-12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrics Cards -->
        <section aria-labelledby="guru-metrics" class="mb-6 lg:mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                <!-- Total Soal di Bank Soal -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Total Soal</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['totalQuestions']) }}</p>
                            <p class="text-xs text-gray-600 mt-2">Soal di bank soal</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V5.25z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6M9 12h6M9 15h6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ujian Aktif -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Ujian Aktif</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['activeExams']) }}</p>
                            <p class="text-xs text-gray-600 mt-2">Ujian yang sedang berlangsung</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-4H3v4a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Siswa -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group sm:col-span-2 lg:col-span-1">
                    <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Total Siswa</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['totalStudents']) }}</p>
                            <p class="text-xs text-gray-600 mt-2">Siswa terdaftar</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 110-8 4 4 0 010 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Additional Stats -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 lg:mb-8">
            <!-- Ujian Selesai -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl transition-shadow">
                <h2 class="text-base md:text-lg font-bold text-gray-900 mb-4 md:mb-5 flex items-center gap-2">
                    <div class="bg-primary/10 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 md:w-5 md:h-5 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 15l3-3 3 3 4-4"/>
                        </svg>
                    </div>
                    <span>Ujian Selesai</span>
                </h2>
                <div class="text-center py-8">
                    <p class="text-4xl md:text-5xl font-bold text-primary mb-2">{{ number_format($metrics['completedExams']) }}</p>
                    <p class="text-sm text-gray-600">Total ujian yang telah diselesaikan</p>
                </div>
            </div>

            <!-- Total Kelas -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl transition-shadow">
                <h2 class="text-base md:text-lg font-bold text-gray-900 mb-4 md:mb-5 flex items-center gap-2">
                    <div class="bg-primary/10 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 md:w-5 md:h-5 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h8"/>
                        </svg>
                    </div>
                    <span>Total Kelas</span>
                </h2>
                <div class="text-center py-8">
                    <p class="text-4xl md:text-5xl font-bold text-primary mb-2">{{ number_format($metrics['totalClasses']) }}</p>
                    <p class="text-sm text-gray-600">Kelas yang tersedia</p>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="mb-6 lg:mb-8" aria-labelledby="quick-actions">
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl transition-shadow">
                <h2 id="quick-actions" class="text-base md:text-lg font-bold text-gray-900 mb-4 md:mb-5 flex items-center gap-2">
                    <div class="bg-primary/10 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 md:w-5 md:h-5 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span>Aksi Cepat</span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('guru.bank') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl hover:from-primary/20 hover:to-primary/10 transition-all duration-300 group">
                        <div class="bg-primary rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V5.25z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6M9 12h6M9 15h6"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1 text-center">Bank Soal</h3>
                        <p class="text-xs text-gray-600 text-center">Kelola soal ujian</p>
                    </a>
                    <a href="{{ route('guru.exams') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl hover:from-primary/20 hover:to-primary/10 transition-all duration-300 group">
                        <div class="bg-primary rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-4H3v4a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1 text-center">Manajemen Ujian</h3>
                        <p class="text-xs text-gray-600 text-center">Buat dan atur ujian</p>
                    </a>
                    <a href="{{ route('guru.results') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl hover:from-primary/20 hover:to-primary/10 transition-all duration-300 group">
                        <div class="bg-primary rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 15l3-3 3 3 4-4"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1 text-center">Hasil Ujian</h3>
                        <p class="text-xs text-gray-600 text-center">Lihat hasil ujian kelas</p>
                    </a>
                    <a href="{{ route('guru.profile') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl hover:from-primary/20 hover:to-primary/10 transition-all duration-300 group">
                        <div class="bg-primary rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4 0-7 2-7 4v2h14v-2c0-2-3-4-7-4z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1 text-center">Profil</h3>
                        <p class="text-xs text-gray-600 text-center">Kelola profil Anda</p>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection