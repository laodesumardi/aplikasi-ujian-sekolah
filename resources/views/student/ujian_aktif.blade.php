@extends('layouts.student')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('siswa.dashboard') }}" class="hover:underline">Dashboard</a>
        <span>/</span>
        <span class="text-white">Ujian Aktif</span>
    </nav>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Ujian Aktif</h1>
                <p class="text-sm text-gray-600 mt-1">
                    @if(Auth::user()->kelas)
                        Ujian untuk kelas: <strong class="text-primary">{{ Auth::user()->kelas }}</strong>
                    @else
                        <span class="text-yellow-600">Anda belum memiliki kelas. Silakan set kelas di <a href="{{ route('siswa.profil') }}" class="underline font-semibold">Profil</a>.</span>
                    @endif
                </p>
            </div>
            @if($availableExams->count() > 0)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ $availableExams->total() }} ujian tersedia
                </span>
            @endif
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
            <form method="GET" action="{{ route('siswa.ujian-aktif') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
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
                </div>
                <div class="relative flex-1 sm:max-w-md">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari ujian..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/></svg>
                    @if(request('subject') && request('subject') != 'all')
                        <input type="hidden" name="subject" value="{{ request('subject') }}">
                    @endif
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors">
                    Cari
                </button>
                @if(request('search') || (request('subject') && request('subject') != 'all'))
                    <a href="{{ route('siswa.ujian-aktif') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Exams Grid -->
        @if($availableExams->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6">
                @foreach($availableExams as $exam)
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
                                @if($exam->total_points)
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Total Poin:</dt>
                                        <dd class="font-medium">{{ $exam->total_points }} poin</dd>
                                    </div>
                                @endif
                            </dl>
                            @if($exam->description)
                                <p class="text-xs text-gray-600 mb-4 line-clamp-2">{{ $exam->description }}</p>
                            @endif
                            @if($exam->kelas || $exam->classRelation)
                                <p class="text-xs text-gray-500 mb-4">
                                    <span class="font-medium">Kelas:</span> {{ $exam->kelas_name ?? '-' }}
                                </p>
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

            <!-- Pagination -->
            <div class="mt-6">
                {{ $availableExams->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-8 md:p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 md:h-16 md:w-16 text-gray-400 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-3-8V3M3.75 5.25h16.5v13.5A2.25 2.25 0 0118 21H6a2.25 2.25 0 01-2.25-2.25V5.25z"/>
                </svg>
                <p class="text-lg md:text-xl font-semibold text-gray-900 mb-2">Tidak ada ujian aktif</p>
                <p class="text-sm md:text-base text-gray-600 mb-2">
                    @if(request('search') || (request('subject') && request('subject') != 'all'))
                        Tidak ada ujian yang sesuai dengan filter Anda.
                    @else
                        @if(Auth::user()->kelas)
                            Belum ada ujian aktif untuk kelas "{{ Auth::user()->kelas }}" saat ini.
                        @else
                            Anda belum memiliki kelas. Silakan set kelas Anda di halaman <a href="{{ route('siswa.profil') }}" class="text-primary hover:underline">Profil</a> untuk melihat ujian yang tersedia.
                        @endif
                    @endif
                </p>
                @if(!Auth::user()->kelas)
                    <p class="text-xs text-gray-500 mb-4">Atau hubungi admin untuk mengatur kelas Anda.</p>
                @endif
                @if(request('search') || (request('subject') && request('subject') != 'all'))
                    <a href="{{ route('siswa.ujian-aktif') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset Filter
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection

