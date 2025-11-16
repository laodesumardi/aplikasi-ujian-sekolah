@extends('layouts.student')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Welcome Section -->
        <div class="mb-6 lg:mb-8">
            <div class="bg-gradient-to-r from-primary via-primary/95 to-primary/90 p-6 md:p-8 lg:p-10 text-white">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 lg:gap-6">
                    <div class="flex-1">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-2 lg:mb-3 leading-tight">Selamat Datang, {{ Auth::user()->name }}!</h1>
                        <p class="text-base md:text-lg lg:text-xl text-white/90 leading-relaxed">
                            @if(Auth::user()->kelas)
                                Kelas {{ Auth::user()->kelas }}
                            @endif
                            - Berikut adalah ringkasan ujian dan statistik Anda.
                        </p>
                    </div>
                    <div class="hidden xl:block flex-shrink-0">
                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-10 h-10 lg:w-12 lg:h-12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3.003.513.069.165.159.329.257.494A3.75 3.75 0 009 12.75V15h2.25a4.5 4.5 0 004.5-4.5V6.042a4.5 4.5 0 00-6-4.332zM19.5 9.75A3 3 0 0122.5 12v1.258l-.29.11c-1.29.51-2.69.702-4.09.54a4.502 4.502 0 01-4.86-4.86c-.162-1.4.03-2.8.54-4.09l.11-.29H19.5z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrics Cards -->
        <section aria-labelledby="siswa-metrics" class="mb-6 lg:mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <!-- Total Ujian Diselesaikan -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Ujian Diselesaikan</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['totalExams']) }}</p>
                            <p class="text-xs text-gray-600 mt-2">Total ujian yang Anda selesaikan</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Rata-rata Nilai -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Rata-rata Nilai</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['averageScore'], 1) }}%</p>
                            <p class="text-xs text-gray-600 mt-2">Rata-rata dari semua ujian</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13l4-4m0 0l4 4m-4-4v9m8-9v9m-6-5v5a2 2 0 002 2h4a2 2 0 002-2v-5"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Nilai Tertinggi -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Nilai Tertinggi</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['highestScore'], 1) }}%</p>
                            <p class="text-xs text-gray-600 mt-2">Nilai terbaik yang pernah dicapai</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.54l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.54l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ujian Tersedia -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Ujian Tersedia</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['availableExamsCount']) }}</p>
                            <p class="text-xs text-gray-600 mt-2">Ujian yang bisa Anda kerjakan</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-3-8V3M3.75 5.25h16.5v13.5A2.25 2.25 0 0118 21H6a2.25 2.25 0 01-2.25-2.25V5.25z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Available Exams Section -->
        <section aria-labelledby="ujian-tersedia" class="mb-6 lg:mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
                <h2 id="ujian-tersedia" class="text-xl md:text-2xl font-bold text-gray-900">Ujian Yang Tersedia</h2>
                <div class="flex items-center gap-3">
                    @if($availableExams->count() > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $availableExams->count() }} ujian aktif
                        </span>
                    @endif
                    <a href="{{ route('siswa.ujian-aktif') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors text-sm">
                        <span>Lihat Semua</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            @if($availableExams->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    @foreach($availableExams->take(6) as $exam)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:scale-[1.02] border border-gray-100">
                            <div class="p-5 md:p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-1">{{ $exam->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $exam->subject->name ?? '-' }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                </div>
                                <dl class="space-y-2 text-sm text-gray-700 mb-4">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Tanggal:</dt>
                                        <dd class="font-medium">{{ $exam->exam_date->format('d/m/Y') }}</dd>
                                    </div>
                                    @if($exam->start_time)
                                        <div class="flex justify-between">
                                            <dt class="text-gray-600">Waktu Mulai:</dt>
                                            <dd class="font-medium">{{ $exam->start_time }}</dd>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Durasi:</dt>
                                        <dd class="font-medium">{{ $exam->duration ?? '-' }} menit</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Jumlah Soal:</dt>
                                        <dd class="font-medium">{{ $exam->total_questions ?? '-' }} soal</dd>
                                    </div>
                                </dl>
                                @if($exam->description)
                                    <p class="text-xs text-gray-600 mb-4 line-clamp-2">{{ $exam->description }}</p>
                                @endif
                            </div>
                            <div class="bg-gray-50 px-5 md:px-6 py-4 border-t border-gray-200">
                                @if(($exam->completed_by_me_count ?? 0) > 0)
                                    <div class="flex items-center gap-3">
                                        <button disabled class="inline-flex items-center justify-center flex-1 gap-2 px-4 py-2 rounded-lg bg-gray-300 text-gray-600 cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Sudah Selesai
                                        </button>
                                        <a href="{{ route('siswa.exam.result', $exam->id) }}" class="inline-flex items-center justify-center flex-1 gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-colors shadow-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Lihat Hasil
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('siswa.exam', $exam->id) }}" class="inline-flex items-center justify-center w-full gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z"/></svg>
                                        Mulai Ujian
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-8 md:p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 md:h-16 md:w-16 text-gray-400 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-3-8V3M3.75 5.25h16.5v13.5A2.25 2.25 0 0118 21H6a2.25 2.25 0 01-2.25-2.25V5.25z"/>
                    </svg>
                    <p class="text-lg md:text-xl font-semibold text-gray-900 mb-2">Tidak ada ujian yang tersedia</p>
                    <p class="text-sm md:text-base text-gray-600">Belum ada ujian aktif untuk kelas Anda saat ini.</p>
                </div>
            @endif
        </section>

        <!-- Scheduled Exams Section -->
        @if($scheduledExams->count() > 0)
        <section aria-labelledby="ujian-terjadwal" class="mb-6 lg:mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
                <h2 id="ujian-terjadwal" class="text-xl md:text-2xl font-bold text-gray-900">Ujian Terjadwal</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $scheduledExams->count() }} ujian mendatang
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                @foreach($scheduledExams as $exam)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="p-5 md:p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-1">{{ $exam->title }}</h3>
                                    <p class="text-sm text-gray-600">{{ $exam->subject->name ?? '-' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Terjadwal
                                </span>
                            </div>
                            <dl class="space-y-2 text-sm text-gray-700 mb-4">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Tanggal:</dt>
                                    <dd class="font-medium">{{ $exam->exam_date->format('d/m/Y') }}</dd>
                                </div>
                                @if($exam->start_time)
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Waktu Mulai:</dt>
                                        <dd class="font-medium">{{ $exam->start_time }}</dd>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Durasi:</dt>
                                    <dd class="font-medium">{{ $exam->duration ?? '-' }} menit</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="bg-gray-50 px-5 md:px-6 py-4 border-t border-gray-200">
                            <button disabled class="inline-flex items-center justify-center w-full gap-2 px-4 py-2 rounded-lg bg-gray-300 text-gray-600 cursor-not-allowed">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Belum Tersedia
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Recent Results Section -->
        @if($examResults->count() > 0)
        <section aria-labelledby="hasil-terkini" class="mb-6 lg:mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
                <h2 id="hasil-terkini" class="text-xl md:text-2xl font-bold text-gray-900">Hasil Ujian Terkini</h2>
                <a href="{{ route('siswa.riwayat') }}" class="inline-flex items-center gap-2 text-sm text-primary hover:underline">
                    Lihat semua
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ujian</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mata Pelajaran</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nilai</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($examResults as $result)
                                @php
                                    $isPassed = $result->percentage >= 60;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $result->exam->title ?? '-' }}</td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $result->exam->subject->name ?? '-' }}</td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="font-bold {{ $isPassed ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($result->percentage, 2) }}%
                                        </span>
                                        <span class="text-gray-500 text-xs">({{ $result->score }}/{{ $result->total_points }})</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                                        @if($isPassed)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Lulus
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Tidak Lulus
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        @if($result->submitted_at)
                                            {{ $result->submitted_at->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @endif
    </div>
@endsection
