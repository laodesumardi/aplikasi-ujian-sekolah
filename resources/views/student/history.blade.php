@extends('layouts.student')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('siswa.dashboard') }}" class="hover:underline">Dashboard</a>
        <span>/</span>
        <span class="text-white">Riwayat Nilai</span>
    </nav>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Riwayat Nilai</h1>
                <p class="text-sm text-gray-600 mt-1">Lihat semua hasil ujian yang telah Anda selesaikan</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-lg p-5 border border-blue-100">
                <p class="text-blue-600 font-semibold text-xs mb-2 uppercase tracking-wide">Total Ujian</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($statistics['totalExams']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-white rounded-xl shadow-lg p-5 border border-green-100">
                <p class="text-green-600 font-semibold text-xs mb-2 uppercase tracking-wide">Rata-rata Nilai</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($statistics['averageScore'], 1) }}%</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-lg p-5 border border-purple-100">
                <p class="text-purple-600 font-semibold text-xs mb-2 uppercase tracking-wide">Nilai Tertinggi</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($statistics['highestScore'], 1) }}%</p>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-white rounded-xl shadow-lg p-5 border border-orange-100">
                <p class="text-orange-600 font-semibold text-xs mb-2 uppercase tracking-wide">Status</p>
                <p class="text-lg font-bold text-gray-900 mb-1">
                    <span class="text-green-600">{{ $statistics['passedCount'] }}</span>
                    <span class="text-gray-400">/</span>
                    <span class="text-red-600">{{ $statistics['failedCount'] }}</span>
                </p>
                <p class="text-xs text-gray-600">Lulus / Tidak Lulus</p>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
            <form method="GET" action="{{ route('siswa.riwayat') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex items-center gap-3 flex-1">
                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Mata Pelajaran:</label>
                    <select name="subject" id="subjectFilter" onchange="this.form.submit()" class="w-full sm:w-auto border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                        <option value="all" {{ request('subject') == 'all' || !request('subject') ? 'selected' : '' }}>Semua</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('status') && request('status') != 'all')
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                </div>
                <div class="flex items-center gap-3 flex-1">
                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Status:</label>
                    <select name="status" id="statusFilter" onchange="this.form.submit()" class="w-full sm:w-auto border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua</option>
                        <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>Lulus (≥60%)</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Tidak Lulus (<60%)</option>
                    </select>
                    @if(request('subject') && request('subject') != 'all')
                        <input type="hidden" name="subject" value="{{ request('subject') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                </div>
                <div class="relative flex-1 sm:max-w-md">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari ujian..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/></svg>
                    @if(request('subject') && request('subject') != 'all')
                        <input type="hidden" name="subject" value="{{ request('subject') }}">
                    @endif
                    @if(request('status') && request('status') != 'all')
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors">
                    Cari
                </button>
                @if(request('search') || (request('subject') && request('subject') != 'all') || (request('status') && request('status') != 'all'))
                    <a href="{{ route('siswa.riwayat') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($results->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ujian</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mata Pelajaran</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nilai</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Persentase</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Waktu Pengerjaan</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($results as $index => $result)
                                @php
                                    $isPassed = $result->percentage >= 60;
                                    $rowNumber = $results->firstItem() + $index;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $rowNumber }}</td>
                                    <td class="px-4 md:px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $result->exam->title ?? '-' }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $result->exam->subject->name ?? '-' }}
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
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                Lulus
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="px-4 md:px-6 py-4 border-t border-gray-200">
                    {{ $results->links() }}
                </div>
            @else
                <div class="p-8 md:p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 md:h-16 md:w-16 text-gray-400 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-lg md:text-xl font-semibold text-gray-900 mb-2">Tidak ada riwayat nilai</p>
                    <p class="text-sm md:text-base text-gray-600 mb-6">
                        @if(request('search') || (request('subject') && request('subject') != 'all') || (request('status') && request('status') != 'all'))
                            Tidak ada hasil yang sesuai dengan filter Anda.
                        @else
                            Anda belum menyelesaikan ujian apapun. Selesaikan ujian untuk melihat riwayat nilai di sini.
                        @endif
                    </p>
                    @if(request('search') || (request('subject') && request('subject') != 'all') || (request('status') && request('status') != 'all'))
                        <a href="{{ route('siswa.riwayat') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reset Filter
                        </a>
                    @else
                        <a href="{{ route('siswa.ujian-aktif') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-3-8V3M3.75 5.25h16.5v13.5A2.25 2.25 0 0118 21H6a2.25 2.25 0 01-2.25-2.25V5.25z"/></svg>
                            Lihat Ujian Aktif
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection