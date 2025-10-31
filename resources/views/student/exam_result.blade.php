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
            background-color: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
    </style>
</head>
<body class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        @php
            $percentage = $examResult->percentage ?? 0;
            $isPassed = $percentage >= 60;
            $percentageFormatted = number_format($percentage, 1);
        @endphp

        <!-- Header Card -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="bg-primary px-6 py-8">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-white/80 text-sm font-medium mb-2">{{ $exam->subject->name ?? 'Mata Pelajaran' }}</p>
                        <h1 class="text-2xl sm:text-3xl font-semibold text-white leading-tight">{{ $exam->title }}</h1>
                    </div>
                </div>
            </div>
            
            <!-- Score Display -->
            <div class="px-6 py-8">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8 mb-6 pb-6 border-b border-gray-100">
                    <div class="text-center sm:text-left">
                        <p class="text-sm text-gray-500 mb-1">Nilai Akhir</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl sm:text-6xl font-light {{ $isPassed ? 'text-success' : 'text-danger' }}">{{ $percentageFormatted }}</span>
                            <span class="text-2xl text-gray-400">%</span>
                        </div>
                    </div>
                    
                    <div class="flex-1 w-full sm:w-auto">
                        <div class="flex items-center gap-6 justify-center sm:justify-start">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Skor</p>
                                <p class="text-xl font-medium text-gray-900">{{ $examResult->score ?? 0 }}</p>
                            </div>
                            <div class="w-px h-8 bg-gray-200"></div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Dari</p>
                                <p class="text-xl font-medium text-gray-900">{{ $examResult->total_points ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center justify-center sm:justify-start gap-2 mb-6">
                    @if($isPassed)
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-200">
                            Lulus
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-red-50 text-red-700 border border-red-200">
                            Tidak Lulus
                        </span>
                    @endif
                    <span class="text-xs text-gray-500">(Minimum 60%)</span>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <!-- Waktu Pengerjaan -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-gray-50 rounded">
                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Waktu Pengerjaan</p>
                        <p class="text-lg font-medium text-gray-900">
                            @if($examResult->time_taken)
                                @php
                                    $hours = floor($examResult->time_taken / 3600);
                                    $minutes = floor(($examResult->time_taken % 3600) / 60);
                                    $seconds = $examResult->time_taken % 60;
                                @endphp
                                @if($hours > 0){{ $hours }}j @endif{{ $minutes }}m {{ $seconds }}s
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tanggal Mulai -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-gray-50 rounded">
                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Dimulai</p>
                        <p class="text-lg font-medium text-gray-900">
                            {{ $examResult->started_at ? $examResult->started_at->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase tracking-wide">Timeline</h3>
            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1">
                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                        <div class="w-px h-12 bg-gray-200 ml-0.5 mt-2"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Mulai Mengerjakan</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $examResult->started_at ? $examResult->started_at->format('d M Y, H:i WIB') : '-' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1">
                        <div class="w-2 h-2 rounded-full {{ $isPassed ? 'bg-success' : 'bg-danger' }}"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Selesai</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $examResult->submitted_at ? $examResult->submitted_at->format('d M Y, H:i WIB') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('siswa.riwayat') }}" class="flex-1 sm:flex-initial px-6 py-3 bg-primary text-white text-center rounded-lg hover:bg-primary/95 transition-colors font-medium">
                Lihat Riwayat
            </a>
            <a href="{{ route('siswa.ujian-aktif') }}" class="flex-1 sm:flex-initial px-6 py-3 bg-white text-gray-700 text-center rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors font-medium">
                Kembali
            </a>
        </div>
    </div>
</body>
</html>