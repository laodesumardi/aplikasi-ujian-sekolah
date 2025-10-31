<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hasil Ujian: {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            overflow-x: hidden;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes progress {
            from {
                stroke-dashoffset: 440;
            }
            to {
                stroke-dashoffset: calc(440 - (440 * var(--progress) / 100));
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.5s ease-out;
        }
        
        .animate-scaleIn {
            animation: scaleIn 0.6s ease-out;
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .progress-ring circle {
            transition: stroke-dashoffset 1.5s ease-in-out;
        }
        
        .primary-gradient {
            background: linear-gradient(135deg, #003f88 0%, #0052b3 100%);
        }
        
        .success-gradient {
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
        }
        
        .danger-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex flex-col items-center justify-center py-8 px-4">
        <div class="w-full max-w-5xl mx-auto">
            <!-- Success Badge -->
            <div class="mb-6 animate-fadeInUp">
                <div class="bg-white border-l-4 border-success rounded-lg shadow-lg p-5 flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success rounded-full flex items-center justify-center shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-gray-900 font-bold text-lg mb-1">Ujian Berhasil Diselesaikan</h3>
                        <p class="text-gray-600 text-sm">Terima kasih telah menyelesaikan ujian ini</p>
                    </div>
                </div>
            </div>

            <!-- Main Result Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden animate-scaleIn">
                <!-- Header -->
                <div class="primary-gradient px-8 md:px-12 py-10 md:py-12 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 right-0 w-72 h-72 bg-white rounded-full -mr-36 -mt-36"></div>
                        <div class="absolute bottom-0 left-0 w-56 h-56 bg-white rounded-full -ml-28 -mb-28"></div>
                    </div>
                    <div class="relative text-center z-10">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4 backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $exam->title }}</h1>
                        <p class="text-white/90 text-lg">{{ $exam->subject->name ?? '-' }}</p>
                    </div>
                </div>

                <!-- Score Section -->
                <div class="px-6 md:px-12 py-10 md:py-12 bg-gray-50">
                    <div class="text-center mb-10">
                        @php
                            $isPassed = $examResult->percentage >= 60;
                            $percentage = number_format($examResult->percentage, 1);
                            $progress = $examResult->percentage;
                        @endphp
                        
                        <!-- Circular Progress Score -->
                        <div class="relative inline-flex items-center justify-center mb-8">
                            <div class="relative w-56 h-56 md:w-64 md:h-64">
                                @php
                                    $radius = 110; // radius untuk ukuran 224x224 (w-56 h-56)
                                    $circumference = 2 * M_PI * $radius;
                                    $offset = $circumference - ($circumference * $progress / 100);
                                @endphp
                                <svg class="progress-ring w-full h-full" viewBox="0 0 224 224">
                                    <circle cx="112" cy="112" r="{{ $radius }}" fill="none" stroke="#e5e7eb" stroke-width="12"/>
                                    <circle 
                                        cx="112" 
                                        cy="112" 
                                        r="{{ $radius }}" 
                                        fill="none" 
                                        stroke="{{ $isPassed ? '#16a34a' : '#dc2626' }}" 
                                        stroke-width="12"
                                        stroke-linecap="round"
                                        stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $offset }}"
                                        style="transition: stroke-dashoffset 1.5s ease-in-out;"
                                    />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-5xl md:text-6xl font-extrabold {{ $isPassed ? 'text-success' : 'text-danger' }} mb-1">
                                            {{ $percentage }}
                                        </div>
                                        <div class="text-2xl font-bold text-gray-500">%</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="mb-6">
                            @if($isPassed)
                                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-success/10 text-success rounded-full text-base font-semibold border-2 border-success/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Lulus - Selamat!</span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-danger/10 text-danger rounded-full text-base font-semibold border-2 border-danger/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span>Tidak Lulus - Belum Memenuhi Syarat (≥60%)</span>
                                </div>
                            @endif
                        </div>

                        <!-- Score Info -->
                        <div class="inline-flex items-center gap-6 px-8 py-4 bg-white rounded-xl shadow-md border border-gray-200">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Nilai Anda</p>
                                <p class="text-3xl font-extrabold text-primary">{{ $examResult->score }}</p>
                            </div>
                            <div class="w-px h-12 bg-gray-300"></div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Poin</p>
                                <p class="text-3xl font-extrabold text-primary">{{ $examResult->total_points }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                        <!-- Total Points Card -->
                        <div class="bg-white rounded-xl p-6 border-2 border-gray-200 shadow-md hover:shadow-lg hover:border-primary/30 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Poin</p>
                                </div>
                            </div>
                            <p class="text-3xl font-extrabold text-gray-900">{{ $examResult->total_points }}</p>
                        </div>

                        <!-- Points Earned Card -->
                        <div class="bg-white rounded-xl p-6 border-2 border-gray-200 shadow-md hover:shadow-lg hover:border-success/30 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Poin Diperoleh</p>
                                </div>
                            </div>
                            <p class="text-3xl font-extrabold text-gray-900">{{ $examResult->score }}</p>
                        </div>

                        <!-- Time Taken Card -->
                        <div class="bg-white rounded-xl p-6 border-2 border-gray-200 shadow-md hover:shadow-lg hover:border-primary/30 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Pengerjaan</p>
                                </div>
                            </div>
                            <p class="text-3xl font-extrabold text-gray-900">
                                @if($examResult->time_taken)
                                    @php
                                        $minutes = floor($examResult->time_taken / 60);
                                        $seconds = $examResult->time_taken % 60;
                                    @endphp
                                    {{ $minutes }}<span class="text-xl text-gray-500">m</span> {{ $seconds }}<span class="text-xl text-gray-500">s</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Exam Info Card -->
                    <div class="bg-white rounded-xl p-6 md:p-8 mb-10 border-2 border-gray-200 shadow-md">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Informasi Ujian
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 font-medium">Tanggal Mulai</span>
                                </div>
                                <span class="font-semibold text-gray-900">
                                    {{ $examResult->started_at ? $examResult->started_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 font-medium">Tanggal Selesai</span>
                                </div>
                                <span class="font-semibold text-gray-900">
                                    {{ $examResult->submitted_at ? $examResult->submitted_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('siswa.riwayat') }}" class="group inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-primary text-white rounded-xl hover:bg-primary/90 transition-all font-semibold shadow-lg hover:shadow-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Lihat Riwayat Nilai
                        </a>
                        <a href="{{ route('siswa.ujian-aktif') }}" class="group inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-semibold shadow-md hover:shadow-lg border-2 border-gray-200 hover:border-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Ujian Aktif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
