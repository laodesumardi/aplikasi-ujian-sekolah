@extends('layouts.teacher')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('guru.dashboard') }}" class="hover:underline">Guru</a>
        <span>/</span>
        <span class="text-white">Bank Soal</span>
    </nav>
@endsection

@section('header-right')
    <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16"/></svg>
        <span>Tambah</span>
    </button>
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

    <section aria-labelledby="bank-soal">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <h1 id="bank-soal" class="text-2xl lg:text-3xl font-bold text-gray-900">Bank Soal</h1>
            <div class="flex items-center gap-2 flex-wrap">
                <button onclick="openImportModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500/30 transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                    <span>Import Soal</span>
                </button>
                <a href="{{ route('guru.bank.template') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    <span>Template Excel</span>
                </a>
                <a href="{{ route('guru.bank.template-doc') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500/30 transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    <span>Template DOC</span>
                </a>
                <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16"/></svg>
                    <span>Tambah Soal Baru</span>
                </button>
            </div>
        </div>

        <!-- Mata Pelajaran Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($subjectsWithQuestions as $subject)
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all border-2 border-transparent hover:border-primary group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary transition-colors mb-2">
                                {{ $subject->name }}
                            </h3>
                            @if($subject->code)
                                <p class="text-sm text-gray-500">Kode: {{ $subject->code }}</p>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-8 h-8 text-primary group-hover:translate-x-1 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">{{ $subject->questions_count }}</span>
                            <span class="text-sm text-gray-500">Soal</span>
                        </div>
                        <div class="ml-auto flex items-center gap-2">
                            <a href="{{ route('guru.bank.detail', $subject->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
                                Lihat Detail
                            </a>
                            <button 
                                onclick="openDeleteSubjectModal({{ $subject->id }}, '{{ addslashes($subject->name) }}', {{ $subject->questions_count }})" 
                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500/30" 
                                title="Hapus Semua Soal">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-1 0l-1 12H8L7 7m5-4a1 1 0 011 1v2H9V4a1 1 0 011-1h2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Soal</h3>
                    <p class="text-gray-600 mb-6">Anda belum membuat soal untuk mata pelajaran apapun.</p>
                    <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16"/></svg>
                        <span>Tambah Soal Pertama</span>
                    </button>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Modals -->
    @php
        $subjects = \App\Models\Subject::orderBy('name')->get();
    @endphp
    @include('teacher.partials.bank_modals', ['subjects' => $subjects])
    
    <!-- Modal: Add Question (OLD - Will be replaced by include) -->
    <div id="addModal_OLD" class="fixed inset-0 z-50 hidden overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeAddModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form method="POST" action="{{ route('guru.bank.store') }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Soal Baru</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                                    <select name="subject_id" id="add_subject_id" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" onchange="toggleOptionsField()">
                                        <option value="">Pilih Mata Pelajaran</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Soal <span class="text-red-500">*</span></label>
                                    <select name="question_type" id="add_question_type" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" onchange="toggleOptionsField()">
                                        <option value="pilihan_ganda">Pilihan Ganda</option>
                                        <option value="essay">Essay</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                                <textarea name="question_text" id="add_question_text" required rows="4" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Masukkan pertanyaan..."></textarea>
                            </div>
                            <div id="add_options_container" class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilihan Jawaban</label>
                                <div id="add_options_list">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-8 text-sm font-medium text-gray-700">A</span>
                                        <input type="text" name="options[A]" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jawaban A">
                                    </div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-8 text-sm font-medium text-gray-700">B</span>
                                        <input type="text" name="options[B]" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jawaban B">
                                    </div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-8 text-sm font-medium text-gray-700">C</span>
                                        <input type="text" name="options[C]" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jawaban C">
                                    </div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-8 text-sm font-medium text-gray-700">D</span>
                                        <input type="text" name="options[D]" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jawaban D">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jawaban Benar <span class="text-red-500">*</span></label>
                                <select name="correct_answer" id="add_correct_answer_pg" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    <option value="">Pilih Jawaban Benar</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                                <input type="text" name="correct_answer" id="add_correct_answer_essay" class="hidden w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Masukkan kunci jawaban untuk essay...">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat (opsional)</label>
                                    <select name="level" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="">Pilih Tingkat</option>
                                        <optgroup label="SD">
                                            <option value="I">I</option>
                                            <option value="II">II</option>
                                            <option value="III">III</option>
                                            <option value="IV">IV</option>
                                            <option value="V">V</option>
                                            <option value="VI">VI</option>
                                        </optgroup>
                                        <optgroup label="SMP">
                                            <option value="VII">VII</option>
                                            <option value="VIII">VIII</option>
                                            <option value="IX">IX</option>
                                        </optgroup>
                                        <optgroup label="SMA">
                                            <option value="X">X</option>
                                            <option value="XI">XI</option>
                                            <option value="XII">XII</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kesulitan <span class="text-red-500">*</span></label>
                                    <select name="difficulty" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="mudah">Mudah</option>
                                        <option value="sedang" selected>Sedang</option>
                                        <option value="sulit">Sulit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Topik (opsional)</label>
                                    <input type="text" name="topic" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Contoh: Aljabar, Teks Eksposisi">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Poin (opsional)</label>
                                    <input type="number" name="points" value="1" min="1" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penjelasan (opsional)</label>
                                <textarea name="explanation" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Penjelasan jawaban..."></textarea>
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

    <!-- Modal: Edit Question -->
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeEditModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Soal</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                                    <select name="subject_id" id="edit_subject_id" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" onchange="toggleOptionsField()">
                                        <option value="">Pilih Mata Pelajaran</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Soal <span class="text-red-500">*</span></label>
                                    <select name="question_type" id="edit_question_type" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" onchange="toggleOptionsField()">
                                        <option value="pilihan_ganda">Pilihan Ganda</option>
                                        <option value="essay">Essay</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                                <textarea name="question_text" id="edit_question_text" required rows="4" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Masukkan pertanyaan..."></textarea>
                            </div>
                            <div id="edit_options_container" class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilihan Jawaban</label>
                                <div id="edit_options_list">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-8 text-sm font-medium text-gray-700">A</span>
                                        <input type="text" name="options[A]" id="edit_option_A" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jawaban A">
                                    </div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-8 text-sm font-medium text-gray-700">B</span>
                                        <input type="text" name="options[B]" id="edit_option_B" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jawaban B">
                                    </div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-8 text-sm font-medium text-gray-700">C</span>
                                        <input type="text" name="options[C]" id="edit_option_C" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jawaban C">
                                    </div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-8 text-sm font-medium text-gray-700">D</span>
                                        <input type="text" name="options[D]" id="edit_option_D" class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jawaban D">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jawaban Benar <span class="text-red-500">*</span></label>
                                <select name="correct_answer" id="edit_correct_answer_pg" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    <option value="">Pilih Jawaban Benar</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                                <input type="text" name="correct_answer" id="edit_correct_answer_essay" class="hidden w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Masukkan kunci jawaban untuk essay...">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat (opsional)</label>
                                    <select name="level" id="edit_level" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="">Pilih Tingkat</option>
                                        <optgroup label="SD">
                                            <option value="I">I</option>
                                            <option value="II">II</option>
                                            <option value="III">III</option>
                                            <option value="IV">IV</option>
                                            <option value="V">V</option>
                                            <option value="VI">VI</option>
                                        </optgroup>
                                        <optgroup label="SMP">
                                            <option value="VII">VII</option>
                                            <option value="VIII">VIII</option>
                                            <option value="IX">IX</option>
                                        </optgroup>
                                        <optgroup label="SMA">
                                            <option value="X">X</option>
                                            <option value="XI">XI</option>
                                            <option value="XII">XII</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kesulitan <span class="text-red-500">*</span></label>
                                    <select name="difficulty" id="edit_difficulty" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="mudah">Mudah</option>
                                        <option value="sedang">Sedang</option>
                                        <option value="sulit">Sulit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Topik (opsional)</label>
                                    <input type="text" name="topic" id="edit_topic" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Contoh: Aljabar, Teks Eksposisi">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Poin (opsional)</label>
                                    <input type="number" name="points" id="edit_points" min="1" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penjelasan (opsional)</label>
                                <textarea name="explanation" id="edit_explanation" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Penjelasan jawaban..."></textarea>
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
                            <h3 class="text-lg font-bold text-gray-900" id="deleteQuestionText">Hapus Soal?</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST" id="deleteForm" class="inline">
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

    <!-- Modal: Import Questions -->
    <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeImportModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('guru.bank.import') }}" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Import Soal</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-red-500">*</span></label>
                                <input type="file" name="file" id="importFileInput" accept=".csv,.xlsx,.xls,.doc,.docx" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" onchange="checkFileType()">
                                <p class="text-xs text-gray-500 mt-1">Format file: CSV, Excel (.xlsx, .xls), Word (.doc, .docx). Maksimal 5MB</p>
                            </div>
                            <div id="subjectSelectionForDoc" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(diperlukan untuk file DOC/DOCX)</span></label>
                                @php
                                    // Ensure we always have subjects list
                                    if (!isset($subjects) || $subjects->count() === 0) {
                                        $subjectsList = \App\Models\Subject::orderBy('name')->get();
                                    } else {
                                        $subjectsList = $subjects;
                                    }
                                @endphp
                                <select name="subject_id" id="importSubjectId" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" required>
                                    <option value="">Pilih Mata Pelajaran</option>
                                    @foreach($subjectsList as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}@if($subject->code) ({{ $subject->code }})@endif</option>
                                    @endforeach
                                </select>
                                @if($subjectsList->count() === 0)
                                    <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-xs text-red-700 mb-1">
                                            <strong>⚠️ Belum ada mata pelajaran!</strong>
                                        </p>
                                        <p class="text-xs text-red-600">
                                            Silakan tambahkan mata pelajaran terlebih dahulu di 
                                            <a href="{{ route('admin.subjects') }}" target="_blank" class="underline font-semibold">halaman Admin > Mata Pelajaran</a>
                                            sebelum melakukan import file DOC/DOCX.
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-sm text-blue-800 mb-2">
                                    <strong>Format yang didukung:</strong>
                                </p>
                                <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                                    <li><strong>CSV</strong> - Comma Separated Values</li>
                                    <li><strong>XLSX</strong> - Excel 2007+</li>
                                    <li><strong>XLS</strong> - Excel 97-2003</li>
                                    <li><strong>DOC/DOCX</strong> - Microsoft Word (format soal standar)</li>
                                </ul>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-sm text-yellow-800 mb-2">
                                    <strong>Format untuk Excel/CSV:</strong>
                                </p>
                                <ul class="text-xs text-yellow-700 space-y-1 list-disc list-inside mb-3">
                                    <li><strong>Mata Pelajaran</strong> - Nama mata pelajaran (harus sudah ada di sistem)</li>
                                    <li><strong>Pertanyaan</strong> - Teks pertanyaan (wajib)</li>
                                    <li><strong>Tipe Soal</strong> - pilihan_ganda atau essay (default: pilihan_ganda)</li>
                                    <li><strong>Opsi A, B, C, D</strong> - Untuk pilihan ganda</li>
                                    <li><strong>Jawaban Benar</strong> - A/B/C/D untuk pilihan ganda, atau kunci jawaban untuk essay</li>
                                    <li><strong>Tingkat</strong> - X, XI, XII (opsional)</li>
                                    <li><strong>Topik</strong> - Topik soal (opsional)</li>
                                    <li><strong>Tingkat Kesulitan</strong> - mudah/sedang/sulit (default: sedang)</li>
                                    <li><strong>Poin</strong> - Poin soal (default: 1)</li>
                                    <li><strong>Penjelasan</strong> - Penjelasan jawaban (opsional)</li>
                                </ul>
                                <p class="text-sm text-yellow-800 mb-2">
                                    <strong>Format untuk DOC/DOCX:</strong>
                                </p>
                                <ul class="text-xs text-yellow-700 space-y-1 list-disc list-inside">
                                    <li>Nomor soal diikuti titik dan spasi (contoh: <code>1. </code>)</li>
                                    <li>Pertanyaan setelah nomor soal</li>
                                    <li>Pilihan A, B, C, D di baris terpisah (format: <code>A. </code>, <code>B. </code>, dst.)</li>
                                    <li>Jawaban benar dengan format: <code>Jawaban: X. [teks jawaban]</code></li>
                                    <li>Contoh format:
                                        <pre class="text-xs mt-1 bg-gray-100 p-2 rounded">1. Pertanyaan di sini...

A. Pilihan A
B. Pilihan B
C. Pilihan C
D. Pilihan D

Jawaban: B. Pilihan B</pre>
                                    </li>
                                </ul>
                            </div>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <p class="text-xs text-green-800">
                                    💡 <strong>Tips:</strong> Download template terlebih dahulu untuk melihat format yang benar. Baris pertama harus berisi header kolom.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-white text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            Import
                        </button>
                        <button type="button" onclick="closeImportModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add Modal
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Import Modal
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }
        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        // Edit Modal
        function openEditModal(id, subjectId, questionText, questionType, options, correctAnswer, level, topic, difficulty, points, explanation) {
            document.getElementById('editForm').action = `{{ url('guru/bank-soal') }}/${id}`;
            document.getElementById('edit_subject_id').value = subjectId || '';
            document.getElementById('edit_question_text').value = questionText || '';
            document.getElementById('edit_question_type').value = questionType || 'pilihan_ganda';
            document.getElementById('edit_level').value = level || '';
            document.getElementById('edit_topic').value = topic || '';
            document.getElementById('edit_difficulty').value = difficulty || 'sedang';
            document.getElementById('edit_points').value = points || 1;
            document.getElementById('edit_explanation').value = explanation || '';
            
            // Clear all options first
            document.getElementById('edit_option_A').value = '';
            document.getElementById('edit_option_B').value = '';
            document.getElementById('edit_option_C').value = '';
            document.getElementById('edit_option_D').value = '';
            
            // Set options - handle both object and array formats
            if (options && typeof options === 'object') {
                // Handle object format {A: 'answer1', B: 'answer2', ...}
                if (options.A !== undefined && options.A !== null) {
                    document.getElementById('edit_option_A').value = String(options.A);
                }
                if (options.B !== undefined && options.B !== null) {
                    document.getElementById('edit_option_B').value = String(options.B);
                }
                if (options.C !== undefined && options.C !== null) {
                    document.getElementById('edit_option_C').value = String(options.C);
                }
                if (options.D !== undefined && options.D !== null) {
                    document.getElementById('edit_option_D').value = String(options.D);
                }
            } else if (Array.isArray(options)) {
                // Handle array format [answer1, answer2, ...]
                if (options[0]) document.getElementById('edit_option_A').value = String(options[0]);
                if (options[1]) document.getElementById('edit_option_B').value = String(options[1]);
                if (options[2]) document.getElementById('edit_option_C').value = String(options[2]);
                if (options[3]) document.getElementById('edit_option_D').value = String(options[3]);
            }
            
            // Set correct answer - must be done after toggleOptionsField
            toggleOptionsField('edit');
            
            // Clear correct answer fields first
            document.getElementById('edit_correct_answer_pg').value = '';
            document.getElementById('edit_correct_answer_essay').value = '';
            
            // Set correct answer based on question type
            if (questionType === 'pilihan_ganda') {
                document.getElementById('edit_correct_answer_pg').value = correctAnswer || '';
            } else {
                document.getElementById('edit_correct_answer_essay').value = correctAnswer || '';
            }
            
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Delete Modal
        function openDeleteModal(id, questionText) {
            document.getElementById('deleteForm').action = `{{ url('guru/bank-soal') }}/${id}`;
            document.getElementById('deleteQuestionText').textContent = `Hapus soal "${questionText}"?`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Toggle options field based on question type
        function toggleOptionsField(prefix = 'add') {
            const questionTypeEl = document.getElementById(`${prefix}_question_type`);
            if (!questionTypeEl) return;
            
            const questionType = questionTypeEl.value;
            const optionsContainer = document.getElementById(`${prefix}_options_container`);
            const correctAnswerPG = document.getElementById(`${prefix}_correct_answer_pg`);
            const correctAnswerEssay = document.getElementById(`${prefix}_correct_answer_essay`);
            
            if (!optionsContainer || !correctAnswerPG || !correctAnswerEssay) return;
            
            if (questionType === 'pilihan_ganda') {
                optionsContainer.classList.remove('hidden');
                correctAnswerPG.classList.remove('hidden');
                correctAnswerEssay.classList.add('hidden');
                // Set required and name attributes
                correctAnswerPG.setAttribute('required', 'required');
                correctAnswerPG.setAttribute('name', 'correct_answer');
                correctAnswerEssay.removeAttribute('required');
                correctAnswerEssay.removeAttribute('name'); // Remove name to prevent submission
                // Clear essay answer when switching to pilihan_ganda
                if (correctAnswerEssay.value) {
                    correctAnswerEssay.value = '';
                }
            } else {
                optionsContainer.classList.add('hidden');
                correctAnswerPG.classList.add('hidden');
                correctAnswerEssay.classList.remove('hidden');
                // Set required and name attributes
                correctAnswerPG.removeAttribute('required');
                correctAnswerPG.removeAttribute('name'); // Remove name to prevent submission
                correctAnswerEssay.setAttribute('required', 'required');
                correctAnswerEssay.setAttribute('name', 'correct_answer');
                // Clear pilihan_ganda answer when switching to essay
                if (correctAnswerPG.value) {
                    correctAnswerPG.value = '';
                }
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            toggleOptionsField('add');
        });

        // Check file type for DOC/DOCX to show subject selection
        function checkFileType() {
            const fileInput = document.getElementById('importFileInput');
            const subjectSelection = document.getElementById('subjectSelectionForDoc');
            const subjectSelect = document.getElementById('importSubjectId');
            const importForm = document.getElementById('importForm');
            
            if (fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name.toLowerCase();
                const extension = fileName.split('.').pop();
                
                if (extension === 'doc' || extension === 'docx') {
                    subjectSelection.classList.remove('hidden');
                    subjectSelect.required = true;
                } else {
                    subjectSelection.classList.add('hidden');
                    subjectSelect.required = false;
                    subjectSelect.value = '';
                }
            }
        }

        // Validate form before submit and show loading
        document.getElementById('importForm')?.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('importFileInput');
            const subjectSelect = document.getElementById('importSubjectId');
            const subjectSelection = document.getElementById('subjectSelectionForDoc');
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name.toLowerCase();
                const extension = fileName.split('.').pop();
                
                if ((extension === 'doc' || extension === 'docx') && !subjectSelect.value) {
                    e.preventDefault();
                    alert('Pilih mata pelajaran untuk file DOC/DOCX');
                    subjectSelection.classList.remove('hidden');
                    return false;
                }
                
                // Show loading state
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
                
                // Re-enable after 30 seconds (in case of timeout)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 30000);
            }
        });

        // Delete Subject Modal (Delete All Questions)
        function openDeleteSubjectModal(subjectId, subjectName, questionsCount) {
            document.getElementById('deleteSubjectForm').action = `{{ url('guru/bank-soal/subject') }}/${subjectId}`;
            document.getElementById('deleteSubjectText').innerHTML = `Hapus semua <strong>${questionsCount} soal</strong> dari mata pelajaran <strong>"${subjectName}"</strong>?`;
            document.getElementById('deleteSubjectModal').classList.remove('hidden');
        }
        function closeDeleteSubjectModal() {
            document.getElementById('deleteSubjectModal').classList.add('hidden');
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDeleteModal();
                closeImportModal();
                closeDeleteSubjectModal();
            }
        });
    </script>

    <!-- Modal: Delete All Questions by Subject -->
    <div id="deleteSubjectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeDeleteSubjectModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-bold text-gray-900">Konfirmasi Hapus</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="deleteSubjectText">Hapus semua soal dari mata pelajaran ini?</p>
                                <p class="text-xs text-red-600 mt-2 font-medium">⚠️ Tindakan ini tidak dapat dibatalkan dan akan menghapus semua soal yang Anda buat untuk mata pelajaran ini.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST" id="deleteSubjectForm" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto">
                            Ya, Hapus Semua
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteSubjectModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection