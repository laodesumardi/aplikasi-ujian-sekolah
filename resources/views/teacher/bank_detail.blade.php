@extends('layouts.teacher')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('guru.dashboard') }}" class="hover:underline">Guru</a>
        <span>/</span>
        <a href="{{ route('guru.bank') }}" class="hover:underline">Bank Soal</a>
        <span>/</span>
        <span class="text-white">{{ $subject->name }}</span>
    </nav>
@endsection

@section('header-right')
    <div class="flex items-center gap-2">
        <a href="{{ route('guru.bank') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Kembali</span>
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

    <section aria-labelledby="bank-soal-detail">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 id="bank-soal-detail" class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">{{ $subject->name }}</h1>
                <p class="text-gray-600">Total: {{ $questions->total() }} soal</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button onclick="openImportModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500/30 transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                    <span>Import Soal</span>
                </button>
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

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
            <form method="GET" action="{{ route('guru.bank.detail', $subject->id) }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex items-center gap-3 flex-1">
                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Tingkat:</label>
                    <select name="level" id="levelFilter" onchange="this.form.submit()" class="w-full sm:w-auto border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                        <option value="all" {{ request('level') == 'all' || !request('level') ? 'selected' : '' }}>Semua</option>
                        <option value="X" {{ request('level') == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ request('level') == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ request('level') == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                </div>
                <div class="relative flex-1 sm:max-w-md">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari soal..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/></svg>
                    @if(request('level') && request('level') != 'all')
                        <input type="hidden" name="level" value="{{ request('level') }}">
                    @endif
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors">
                    Cari
                </button>
                @if(request('search') || (request('level') && request('level') != 'all'))
                    <a href="{{ route('guru.bank.detail', $subject->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none transition-colors">
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
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Soal</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Topik</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tingkat</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tipe</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tingkat Kesulitan</th>
                            <th class="px-4 md:px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($questions as $question)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $question->id }}</td>
                                <td class="px-4 md:px-6 py-4 text-sm text-gray-600 max-w-xs">{{ Str::limit(strip_tags($question->question_text), 60) }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $question->topic ?? '-' }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $question->level ?? '-' }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $typeColors = ['pilihan_ganda' => 'bg-blue-100 text-blue-800', 'essay' => 'bg-purple-100 text-purple-800'];
                                        $typeLabels = ['pilihan_ganda' => 'Pilihan Ganda', 'essay' => 'Essay'];
                                        $color = $typeColors[$question->question_type] ?? 'bg-gray-100 text-gray-800';
                                        $label = $typeLabels[$question->question_type] ?? ucfirst($question->question_type);
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $difficultyColors = ['mudah' => 'bg-green-100 text-green-800', 'sedang' => 'bg-yellow-100 text-yellow-800', 'sulit' => 'bg-red-100 text-red-800'];
                                        $color = $difficultyColors[$question->difficulty] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">
                                        {{ ucfirst($question->difficulty) }}
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick='openEditModal({{ $question->id }}, {{ $question->subject_id }}, @json($question->question_text), @json($question->question_type), @json($question->options ?? []), @json($question->correct_answer ?? ""), @json($question->level ?? ""), @json($question->topic ?? ""), @json($question->difficulty), {{ $question->points }}, @json($question->explanation ?? ""))' class="inline-flex items-center px-2 py-1.5 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487l3.651 3.651m-2.514-4.39l-9.803 9.804a4.5 4.5 0 00-1.253 2.303l-.51 2.553 2.553-.51a4.5 4.5 0 002.303-1.253l9.803-9.803"/></svg>
                                        </button>
                                        <button onclick='openDeleteModal({{ $question->id }}, @json(Str::limit($question->question_text, 50)))' class="inline-flex items-center px-2 py-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-1 0l-1 12H8L7 7m5-4a1 1 0 011 1v2H9V4a1 1 0 011-1h2z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 md:px-6 py-12 text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5A2.25 2.25 0 0118 21H6a2.25 2.25 0 01-2.25-2.25V5.25z"/>
                                    </svg>
                                    <p class="text-sm">Tidak ada soal ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($questions->hasPages())
                <div class="px-4 md:px-6 py-4 border-t border-gray-200">
                    {{ $questions->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- Modals -->
    @include('teacher.partials.bank_modals', ['subjects' => $subjects, 'subject' => $subject])
    
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
                correctAnswerPG.setAttribute('required', 'required');
                correctAnswerPG.setAttribute('name', 'correct_answer');
                correctAnswerEssay.removeAttribute('required');
                correctAnswerEssay.removeAttribute('name');
                if (correctAnswerEssay.value) {
                    correctAnswerEssay.value = '';
                }
            } else {
                optionsContainer.classList.add('hidden');
                correctAnswerPG.classList.add('hidden');
                correctAnswerEssay.classList.remove('hidden');
                correctAnswerPG.removeAttribute('required');
                correctAnswerPG.removeAttribute('name');
                correctAnswerEssay.setAttribute('required', 'required');
                correctAnswerEssay.setAttribute('name', 'correct_answer');
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
                
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 30000);
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDeleteModal();
                closeImportModal();
            }
        });
    </script>
@endsection

