@extends('layouts.teacher')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('guru.dashboard') }}" class="hover:underline">Guru</a>
        <span>/</span>
        <span class="text-white">Manajemen Ujian</span>
    </nav>
@endsection

@section('header-right')
    <div class="flex items-center gap-2">
        <a href="{{ route('guru.results') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l3-3 3 3 4-4"/></svg>
            <span>Hasil Ujian</span>
        </a>
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16"/></svg>
            <span>Tambah</span>
        </button>
    </div>
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

    <section aria-labelledby="manajemen-ujian">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <h1 id="manajemen-ujian" class="text-2xl lg:text-3xl font-bold text-gray-900">Manajemen Ujian (Jadwal)</h1>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('guru.results') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500/30 transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l3-3 3 3 4-4"/></svg>
                    <span>Lihat Hasil Ujian</span>
                </a>
                <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14v6M9 17h6"/></svg>
                    <span>Buat Jadwal Ujian Baru</span>
                </button>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
            <form method="GET" action="{{ route('guru.exams') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
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
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
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
                    <a href="{{ route('guru.exams') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Judul</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kelas</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Durasi</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Soal</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 md:px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($exams as $exam)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $exam->title }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $exam->subject->name ?? '-' }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $exam->kelas_name ?? '-' }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $exam->exam_date->format('d/m/Y') }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ is_string($exam->start_time) ? substr($exam->start_time, 0, 5) : $exam->start_time }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $exam->duration }} menit</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $exam->total_questions > 0 ? $exam->total_questions : ($exam->questions->count() ?? 0) }} soal</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'scheduled' => 'bg-blue-100 text-blue-800',
                                            'active' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-purple-100 text-purple-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusLabels = [
                                            'draft' => 'Draft',
                                            'scheduled' => 'Terjadwal',
                                            'active' => 'Aktif',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                        $color = $statusColors[$exam->status] ?? 'bg-gray-100 text-gray-800';
                                        $label = $statusLabels[$exam->status] ?? ucfirst($exam->status);
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('guru.exams.sync-questions', $exam->id) }}" onsubmit="return confirm('Sinkronkan soal ujian ini dengan bank soal? Semua soal dari mata pelajaran ini akan diambil dari bank soal.');" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-2 py-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Sinkronkan dengan Bank Soal">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </button>
                                        </form>
                                        <button onclick='openEditModal({{ $exam->id }}, @json($exam->title), {{ $exam->subject_id }}, {{ $exam->class_id ?? "null" }}, @json($exam->kelas ?? ""), @json($exam->description ?? ""), @json($exam->exam_date->format("Y-m-d")), @json(is_string($exam->start_time) ? substr($exam->start_time, 0, 5) : $exam->start_time), {{ $exam->duration }}, @json($exam->status), @json($exam->questions->pluck("id")->toArray()))' class="inline-flex items-center px-2 py-1.5 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487l3.651 3.651m-2.514-4.39l-9.803 9.804a4.5 4.5 0 00-1.253 2.303l-.51 2.553 2.553-.51a4.5 4.5 0 002.303-1.253l9.803-9.803"/></svg>
                                        </button>
                                        <button onclick='openDeleteModal({{ $exam->id }}, @json($exam->title))' class="inline-flex items-center px-2 py-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-1 0l-1 12H8L7 7m5-4a1 1 0 011 1v2H9V4a1 1 0 011-1h2z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 md:px-6 py-12 text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm">Tidak ada jadwal ujian ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($exams->hasPages())
                <div class="px-4 md:px-6 py-4 border-t border-gray-200">
                    {{ $exams->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- Modal: Add Exam -->
    <div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeAddModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <form method="POST" action="{{ route('guru.exams.store') }}" id="addExamForm">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Buat Jadwal Ujian Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Ujian <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="add_title" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Contoh: Ujian Matematika Bab 1">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                                    <select name="subject_id" id="add_subject_id" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="">Pilih Mata Pelajaran</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Semua soal dari mata pelajaran ini akan otomatis ditambahkan ke ujian.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                                    <div class="flex gap-2">
                                        <select name="class_id" id="add_class_id" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                            <option value="">Pilih dari Daftar</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="self-center text-gray-500">atau</span>
                                        <input type="text" name="kelas" id="add_kelas" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Masukkan nama kelas">
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ujian <span class="text-red-500">*</span></label>
                                    <input type="date" name="exam_date" id="add_exam_date" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai <span class="text-red-500">*</span></label>
                                    <input type="time" name="start_time" id="add_start_time" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit) <span class="text-red-500">*</span></label>
                                    <input type="number" name="duration" id="add_duration" required min="1" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="90">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (opsional)</label>
                                <textarea name="description" id="add_description" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Deskripsi ujian..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-white text-sm font-medium hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto">
                            Simpan
                        </button>
                        <button type="button" onclick="closeAddModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Exam -->
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeEditModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <form method="POST" id="editExamForm">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Jadwal Ujian</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Ujian <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="edit_title" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                                    <select name="subject_id" id="edit_subject_id" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" onchange="loadQuestions('edit')">
                                        <option value="">Pilih Mata Pelajaran</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                                    <div class="flex gap-2">
                                        <select name="class_id" id="edit_class_id" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                            <option value="">Pilih dari Daftar</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="self-center text-gray-500">atau</span>
                                        <input type="text" name="kelas" id="edit_kelas" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Masukkan nama kelas">
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ujian <span class="text-red-500">*</span></label>
                                    <input type="date" name="exam_date" id="edit_exam_date" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai <span class="text-red-500">*</span></label>
                                    <input type="time" name="start_time" id="edit_start_time" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit) <span class="text-red-500">*</span></label>
                                    <input type="number" name="duration" id="edit_duration" required min="1" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                                <select name="status" id="edit_status" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    <option value="draft">Draft</option>
                                    <option value="scheduled">Terjadwal</option>
                                    <option value="active">Aktif</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (opsional)</label>
                                <textarea name="description" id="edit_description" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Deskripsi ujian..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Soal <span class="text-xs text-gray-500">(opsional)</span></label>
                                <div id="edit_questions_container" class="border rounded-lg p-4 max-h-60 overflow-y-auto bg-gray-50">
                                    <p class="text-sm text-gray-500">Memuat soal...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-white text-sm font-medium hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto">
                            Update
                        </button>
                        <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Delete Confirmation -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeDeleteModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-bold text-gray-900" id="deleteExamText">Hapus Jadwal Ujian?</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus jadwal ujian ini? Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST" id="deleteExamForm" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto">
                            Hapus
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add Modal
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            // Reset form
            document.getElementById('addExamForm').reset();
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Edit Modal
        function openEditModal(id, title, subjectId, classId, kelas, description, examDate, startTime, duration, status, questionIds) {
            document.getElementById('editExamForm').action = `{{ url('guru/ujian') }}/${id}`;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_subject_id').value = subjectId;
            document.getElementById('edit_class_id').value = classId || '';
            document.getElementById('edit_kelas').value = kelas || '';
            document.getElementById('edit_description').value = description || '';
            document.getElementById('edit_exam_date').value = examDate;
            document.getElementById('edit_start_time').value = startTime;
            document.getElementById('edit_duration').value = duration;
            document.getElementById('edit_status').value = status;
            
            // Load questions for selected subject
            if (subjectId) {
                loadQuestions('edit', questionIds || []);
            }
            
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Delete Modal
        function openDeleteModal(id, title) {
            document.getElementById('deleteExamForm').action = `{{ url('guru/ujian') }}/${id}`;
            document.getElementById('deleteExamText').textContent = `Hapus jadwal ujian "${title}"?`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Load questions based on subject
        function loadQuestions(prefix = 'add', selectedIds = []) {
            const subjectId = document.getElementById(`${prefix}_subject_id`).value;
            const container = document.getElementById(`${prefix}_questions_container`);
            
            if (!subjectId) {
                container.innerHTML = '<p class="text-sm text-gray-500">Pilih mata pelajaran terlebih dahulu untuk melihat daftar soal.</p>';
                return;
            }
            
            container.innerHTML = '<p class="text-sm text-gray-500">Memuat soal...</p>';
            
            fetch(`{{ route('guru.exams.questions') }}?subject_id=${subjectId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.questions && data.questions.length > 0) {
                        let html = '<div class="space-y-2">';
                        data.questions.forEach(question => {
                            const isChecked = selectedIds.includes(question.id);
                            html += `
                                <label class="flex items-start gap-2 p-2 hover:bg-white rounded cursor-pointer">
                                    <input type="checkbox" name="question_ids[]" value="${question.id}" ${isChecked ? 'checked' : ''} class="mt-1 rounded border-gray-300 text-primary focus:ring-primary">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">${question.text}</p>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                            <span>${question.type === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay'}</span>
                                            <span>•</span>
                                            <span>${question.points} poin</span>
                                            <span>•</span>
                                            <span>${question.difficulty}</span>
                                        </div>
                                    </div>
                                </label>
                            `;
                        });
                        html += '</div>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<p class="text-sm text-gray-500">Tidak ada soal untuk mata pelajaran ini. <a href="{{ route("guru.bank") }}" class="text-primary underline">Tambah soal di Bank Soal</a></p>';
                    }
                })
                .catch(error => {
                    container.innerHTML = '<p class="text-sm text-red-500">Error memuat soal. Silakan refresh halaman.</p>';
                });
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDeleteModal();
            }
        });
    </script>
@endsection