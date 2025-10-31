@extends('layouts.teacher')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('guru.dashboard') }}" class="hover:underline">Guru</a>
        <span>/</span>
        <a href="{{ route('guru.results') }}" class="hover:underline">Hasil Ujian</a>
        <span>/</span>
        <span class="text-white">Detail</span>
    </nav>
@endsection

@section('content')
    <section aria-labelledby="detail-hasil-ujian">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 id="detail-hasil-ujian" class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $exam->title }}</h1>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $exam->subject->name ?? '-' }} • {{ $exam->kelas_name ?? '-' }} • {{ $exam->exam_date->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if($results->count() > 0)
                <a href="{{ route('guru.results.export', $exam->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    <span>Export Excel</span>
                </a>
                <form method="POST" action="{{ route('guru.results.delete-all', $exam->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua hasil ujian ini? Tindakan ini tidak dapat dibatalkan.');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus Semua Hasil</span>
                    </button>
                </form>
                @endif
                <a href="{{ route('guru.results') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-lg p-5 border border-blue-100">
                <p class="text-blue-600 font-semibold text-xs mb-2 uppercase tracking-wide">Total Siswa</p>
                <p class="text-3xl font-bold text-gray-900">{{ $statistics['total_students'] }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-white rounded-xl shadow-lg p-5 border border-green-100">
                <p class="text-green-600 font-semibold text-xs mb-2 uppercase tracking-wide">Rata-rata Nilai</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($statistics['average_score'], 1) }}</p>
                <p class="text-sm text-gray-600 mt-1">({{ number_format($statistics['average_percentage'], 1) }}%)</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-lg p-5 border border-purple-100">
                <p class="text-purple-600 font-semibold text-xs mb-2 uppercase tracking-wide">Nilai Tertinggi</p>
                <p class="text-3xl font-bold text-gray-900">{{ $statistics['highest_score'] }}</p>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-white rounded-xl shadow-lg p-5 border border-red-100">
                <p class="text-red-600 font-semibold text-xs mb-2 uppercase tracking-wide">Nilai Terendah</p>
                <p class="text-3xl font-bold text-gray-900">{{ $statistics['lowest_score'] }}</p>
            </div>
        </div>

        <!-- Pass/Fail Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 font-semibold text-sm mb-1">Lulus (≥60%)</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $statistics['passed'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">siswa</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-8 h-8 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-600 font-semibold text-sm mb-1">Tidak Lulus (<60%)</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $statistics['failed'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">siswa</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-8 h-8 text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Results Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Daftar Nilai Siswa</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Rank</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nilai</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Persentase</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Waktu Pengerjaan</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Waktu Submit</th>
                            <th class="px-4 md:px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($results as $index => $result)
                            @php
                                $isPassed = $result->percentage >= 60;
                                $rank = $index + 1;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $rank <= 3 ? 'bg-yellow-50' : '' }}">
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($rank == 1)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-400 text-yellow-900 font-bold">1</span>
                                    @elseif($rank == 2)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-gray-900 font-bold">2</span>
                                    @elseif($rank == 3)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-300 text-orange-900 font-bold">3</span>
                                    @else
                                        <span class="text-gray-600">{{ $rank }}</span>
                                    @endif
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @php
                                                $studentAvatarUrl = null;
                                                if (!empty($result->student->avatar)) {
                                                    $file = public_path($result->student->avatar);
                                                    if ($file && file_exists($file)) {
                                                        $studentAvatarUrl = app()->environment('production') ? secure_asset($result->student->avatar) : asset($result->student->avatar);
                                                    }
                                                }
                                            @endphp
                                            @if($studentAvatarUrl)
                                                <img src="{{ $studentAvatarUrl }}" alt="{{ $result->student->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-primary/10 border-2 border-gray-200 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6 text-primary">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $result->student->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="font-bold text-gray-900">{{ $result->score }}</span>
                                    <span class="text-gray-500 text-xs">/ {{ $result->total_points }}</span>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="font-semibold {{ $isPassed ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($result->percentage, 2) }}%
                                    </span>
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
                                    @if($result->time_taken)
                                        @php
                                            $minutes = floor($result->time_taken / 60);
                                            $seconds = $result->time_taken % 60;
                                        @endphp
                                        {{ $minutes }}m {{ $seconds }}s
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($result->submitted_at)
                                        {{ $result->submitted_at->format('d/m/Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('guru.results.delete', $result->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hasil ujian siswa {{ $result->student->name }}? Tindakan ini tidak dapat dibatalkan.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-2 py-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-1 0l-1 12H8L7 7m5-4a1 1 0 011 1v2H9V4a1 1 0 011-1h2z"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 md:px-6 py-12 text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm">Belum ada siswa yang menyelesaikan ujian ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

