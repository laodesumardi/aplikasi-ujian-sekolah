<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian: {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header dengan Timer -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-900">{{ $exam->title }}</h1>
                <p class="text-sm text-gray-600">{{ $exam->subject->name ?? '-' }} | Durasi: {{ $exam->duration }} menit</p>
            </div>
            <div id="timerBox" class="flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg shadow border-2 border-red-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path d="M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    <path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zm0 2.25a7.5 7.5 0 110 15 7.5 7.5 0 010-15z" clip-rule="evenodd" />
        </svg>
                <span class="font-mono font-semibold text-lg" id="timerDisplay">00:00:00</span>
            </div>
    </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
        <!-- Question Column (70%) -->
            <div class="flex-1 lg:w-2/3">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <!-- Question content -->
                    <div id="questionArea" class="space-y-6">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3 flex-1">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary text-white font-bold text-lg" id="currentNumber">1</span>
                                <h2 class="text-xl font-semibold text-gray-900 flex-1" id="questionText">Memuat soal...</h2>
                            </div>
                            <button id="bookmarkBtn" type="button" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-gray-300 hover:border-yellow-400 hover:bg-yellow-50 transition-colors" title="Tandai soal untuk ditinjau kembali">
                                <svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                                </svg>
                            </button>
                        </div>

                        <div id="optionsArea" class="space-y-3">
                            <!-- Options injected by JS -->
                        </div>

                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                            <button id="prevBtn" class="px-6 py-2.5 rounded-lg bg-gray-300 text-gray-900 hover:bg-gray-400 transition-colors font-medium">Soal Sebelumnya</button>
                            <button id="nextBtn" class="px-6 py-2.5 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors font-medium">Soal Selanjutnya</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Sidebar (30%) -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Navigasi Soal</h3>
                    
                    <!-- Status Summary -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-600">Total Soal:</span>
                            <span class="font-semibold text-gray-900" id="totalQuestions">{{ $questions->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-green-600">Sudah Dikerjakan:</span>
                            <span class="font-semibold text-green-700" id="answeredCount">0</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-yellow-600">Ditandai:</span>
                            <span class="font-semibold text-yellow-700" id="bookmarkedCount">0</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Belum Dikerjakan:</span>
                            <span class="font-semibold text-gray-700" id="unansweredCount">{{ $questions->count() }}</span>
                        </div>
                    </div>

                    <div id="navGrid" class="grid grid-cols-5 gap-2 mb-6 max-h-96 overflow-y-auto">
                        <!-- Numbered boxes injected by JS -->
                    </div>

                    <div class="mt-auto">
                        <form id="submitForm" method="POST" action="{{ route('siswa.exam.submit', $exam->id) }}">
                            @csrf
                            <button type="button" id="finishBtn" class="w-full px-4 py-3 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors font-semibold shadow-md">
                                Selesai Ujian
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Finish Confirmation Modal -->
    <div id="finishConfirmModal" class="hidden fixed inset-0 z-50 overflow-y-auto opacity-0 transition-opacity duration-200" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="hideFinishConfirmModal()"></div>
            
            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Modal panel -->
            <div id="modalContent" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all scale-95 sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white" id="modal-title">Konfirmasi Penyelesaian Ujian</h3>
                        </div>
                        <button onclick="hideFinishConfirmModal()" class="text-white/80 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Body -->
                <div class="bg-white px-6 py-6">
                    <p class="text-gray-700 mb-6">Anda yakin ingin menyelesaikan ujian ini? Pastikan semua jawaban sudah benar.</p>
                    
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span class="text-sm font-medium text-blue-600">Total Soal</span>
                            </div>
                            <p class="text-2xl font-bold text-blue-900" id="modalTotalQuestions">0</p>
                        </div>
                        
                        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-green-600">Sudah Dikerjakan</span>
                            </div>
                            <p class="text-2xl font-bold text-green-900" id="modalAnswered">0</p>
                        </div>
                        
                        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-sm font-medium text-yellow-600">Ditandai</span>
                            </div>
                            <p class="text-2xl font-bold text-yellow-900" id="modalBookmarked">0</p>
                        </div>
                        
                        <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-600">Belum Dikerjakan</span>
                            </div>
                            <p class="text-2xl font-bold text-gray-900" id="modalUnanswered">0</p>
            </div>
        </div>

                    <!-- Warning message -->
                    <div id="modalWarning" class="mb-6"></div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button onclick="hideFinishConfirmModal()" class="px-5 py-2.5 rounded-lg font-medium text-gray-700 bg-white border-2 border-gray-300 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Batal
                    </button>
                    <button onclick="confirmFinishExam()" class="px-5 py-2.5 rounded-lg font-medium text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transform hover:scale-105">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Ya, Selesaikan Ujian</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Exam data
        const examId = {{ $exam->id }};
        const durationMinutes = {{ $exam->duration }};
        const questions = @json($questionsData);
        const savedAnswers = @json($answers ?? []);
        const totalQuestions = questions.length;
        
        // Debug: Log all questions data
        console.log('All questions data:', questions);
        questions.forEach((q, idx) => {
            console.log(`Question ${idx + 1}:`, {
                id: q.id,
                type: q.type,
                options: q.options,
                optionsType: typeof q.options,
                isArray: Array.isArray(q.options),
                isObject: typeof q.options === 'object' && q.options !== null,
                optionsKeys: q.options ? Object.keys(q.options) : []
            });
        });

        let currentIndex = 0;
        const answers = {};
        const bookmarked = {}; // Store bookmarked question IDs

        // Load saved answers
        Object.keys(savedAnswers).forEach(questionId => {
            answers[questionId] = savedAnswers[questionId];
        });

        // Load bookmarked questions from localStorage
        const savedBookmarks = localStorage.getItem(`exam_${examId}_bookmarks`);
        if (savedBookmarks) {
            try {
                const bookmarks = JSON.parse(savedBookmarks);
                Object.keys(bookmarks).forEach(qId => {
                    if (bookmarks[qId]) {
                        bookmarked[qId] = true;
                    }
                });
            } catch (e) {
                console.error('Error loading bookmarks:', e);
            }
        }

        const questionTextEl = document.getElementById('questionText');
        const currentNumberEl = document.getElementById('currentNumber');
        const optionsAreaEl = document.getElementById('optionsArea');
        const navGridEl = document.getElementById('navGrid');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const finishBtn = document.getElementById('finishBtn');
        const bookmarkBtn = document.getElementById('bookmarkBtn');
        const bookmarkIcon = document.getElementById('bookmarkIcon');
        const timerDisplay = document.getElementById('timerDisplay');
        const answeredCountEl = document.getElementById('answeredCount');
        const unansweredCountEl = document.getElementById('unansweredCount');
        const bookmarkedCountEl = document.getElementById('bookmarkedCount');

        function renderQuestion(index) {
            if (index < 0 || index >= totalQuestions) return;
            
            const q = questions[index];
            currentNumberEl.textContent = index + 1;
            questionTextEl.innerHTML = q.text || 'Memuat soal...'; // Use innerHTML for rich text

            // Clear options area completely
            optionsAreaEl.innerHTML = '';
            
            // Debug: Log question data
            console.log('Question data:', {
                id: q.id,
                type: q.type,
                hasOptions: !!q.options,
                options: q.options
            });
            
            // Check question type first
            if (q.type === 'pilihan_ganda') {
                // For multiple choice, check if options exist
                let optionsList = [];
                
                if (q.options) {
                    // Handle different option formats
                    if (Array.isArray(q.options)) {
                        // If it's an array, convert to object format (limit to A-D only)
                        q.options.slice(0, 4).forEach((opt, idx) => {
                            const key = String.fromCharCode(65 + idx); // A, B, C, D
                            let value = '';
                            if (typeof opt === 'string') {
                                value = opt;
                            } else if (opt && typeof opt === 'object') {
                                value = opt.label || opt.text || opt.value || String(opt);
                            } else {
                                value = String(opt);
                            }
                            // Always add option, even if empty (to maintain A-D structure)
                            optionsList.push({ key: key, value: value.trim() });
                            console.log(`Added array option ${key}: ${value.trim() || '(empty)'}`);
                        });
                    } else if (typeof q.options === 'object' && q.options !== null) {
                        // If it's an object like {'A': 'answer1', 'B': 'answer2'}
                        console.log('Processing object options:', q.options);
                        console.log('Object keys:', Object.keys(q.options));
                        
                        // First, try to get options by letter keys (A, B, C, D only - limit to D)
                        const optionKeys = ['A', 'B', 'C', 'D'];
                        optionKeys.forEach((key) => {
                            // Check multiple ways: direct property, hasOwnProperty, in operator
                            if ((q.options.hasOwnProperty && q.options.hasOwnProperty(key)) || 
                                (key in q.options) || 
                                (q.options[key] !== undefined && q.options[key] !== null)) {
                                let value = String(q.options[key] || '').trim();
                                // Remove 'null' and 'undefined' strings
                                if (value === 'null' || value === 'undefined') {
                                    value = '';
                                }
                                console.log(`Checking key ${key}:`, value);
                                // Always add the option, even if empty (to maintain A-D structure)
                                optionsList.push({ 
                                    key: key, 
                                    value: value 
                                });
                                console.log(`Added option ${key}: ${value || '(empty)'}`);
                            }
                        });
                        
                        // If no options found with letter keys, try to iterate over all keys
                        if (optionsList.length === 0) {
                            console.log('No options found with letter keys, trying all keys...');
                            Object.keys(q.options).forEach((key, idx) => {
                                if (idx >= 4) return; // Limit to first 4 items
                                const value = String(q.options[key] || '').trim();
                                console.log(`Checking key "${key}":`, value);
                                // If key is a letter (A-D only), use it directly
                                if (/^[A-D]$/i.test(key)) {
                                    if (value !== 'null' && value !== 'undefined') {
                                        optionsList.push({ 
                                            key: key.toUpperCase(), 
                                            value: value 
                                        });
                                        console.log(`Added option ${key.toUpperCase()}: ${value}`);
                                    }
                                } else {
                                    // Otherwise, map numeric indices to letters (A-D only)
                                    const optionKey = String.fromCharCode(65 + idx); // A, B, C, D
                                    if (value !== 'null' && value !== 'undefined') {
                                        optionsList.push({ 
                                            key: optionKey, 
                                            value: value 
                                        });
                                        console.log(`Added option ${optionKey} (from key "${key}"): ${value}`);
                                    }
                                }
                            });
                        }
                        
                        // Ensure we have at least A, B, C, D entries even if empty
                        // This ensures all options are displayed
                        const existingKeys = optionsList.map(opt => opt.key);
                        optionKeys.forEach((key) => {
                            if (!existingKeys.includes(key)) {
                                optionsList.push({ 
                                    key: key, 
                                    value: '' 
                                });
                                console.log(`Added empty option ${key} to ensure all options are shown`);
                            }
                        });
                        
                        // Sort options by key (A, B, C, D)
                        optionsList.sort((a, b) => a.key.localeCompare(b.key));
                    } else if (typeof q.options === 'string') {
                        // If options is a string, try to parse it
                        console.warn('Options is a string, attempting to parse:', q.options);
                        // Split by common patterns like "A.", "B.", etc.
                        // Pattern: Letter followed by dot, then capture everything until next letter+dot or end
                        // Support various separators: ".", ")", ":", "-", or just spaces
                        // Limit to A-D only
                        const pattern = /([A-D])\s*[\.\)\:\-]?\s*([^A-E]+?)(?=\s*[A-D]\s*[\.\)\:\-]?|$)/gi;
                        let match;
                        let lastIndex = 0;
                        while ((match = pattern.exec(q.options)) !== null && optionsList.length < 4) {
                            const key = match[1].toUpperCase();
                            // Only add A, B, C, D
                            if (['A', 'B', 'C', 'D'].includes(key)) {
                                optionsList.push({
                                    key: key,
                                    value: match[2].trim()
                                });
                                console.log(`Parsed option ${key}: ${match[2].trim()}`);
                                lastIndex = pattern.lastIndex;
                            }
                        }
                    }
                }
                
                // Ensure all A, B, C, D options are present in optionsList
                const requiredKeys = ['A', 'B', 'C', 'D'];
                const existingKeys = optionsList.map(opt => opt.key);
                requiredKeys.forEach((key) => {
                    if (!existingKeys.includes(key)) {
                        optionsList.push({ key: key, value: '' });
                        console.log(`Added missing option ${key} with empty value`);
                    }
                });
                
                // Sort options by key (A, B, C, D)
                optionsList.sort((a, b) => a.key.localeCompare(b.key));
                
                // Render multiple choice options (limit to A-D only)
                // Filter to only A-D, but keep empty values if at least one option has content
                const hasAnyContent = optionsList.some(opt => opt.value && opt.value.trim() !== '');
                const filteredOptions = optionsList
                    .filter(opt => ['A', 'B', 'C', 'D'].includes(opt.key))
                    .filter(opt => {
                        // Show option if:
                        // 1. It has content, OR
                        // 2. At least one other option has content (to maintain A-D structure), OR
                        // 3. It's the only option
                        return opt.value.trim() !== '' || hasAnyContent || optionsList.length === 1;
                    });
                
                console.log('Options list before filter:', optionsList);
                console.log('Has any content:', hasAnyContent);
                console.log('Filtered options before render:', filteredOptions);
                
                if (filteredOptions.length > 0) {
                    filteredOptions.forEach((option) => {
                const wrapper = document.createElement('label');
                        wrapper.className = 'flex items-center gap-3 px-4 py-3 rounded-lg border-2 border-gray-200 hover:border-primary hover:bg-primary/5 cursor-pointer transition-all';

                const radio = document.createElement('input');
                radio.type = 'radio';
                        radio.name = `q_${q.id}`;
                        radio.id = `q${q.id}_${option.key}`;
                        radio.value = option.key;
                        radio.checked = answers[q.id] === option.key;
                radio.addEventListener('change', () => {
                            answers[q.id] = option.key;
                            saveAnswer(q.id, option.key);
                    updateNavBox(index);
                            updateCounts();
                });

                const text = document.createElement('span');
                        text.className = 'flex-1 text-gray-900';
                        // Ensure text content is properly set (not concatenated)
                        // Display option text, or show placeholder if empty
                        if (option.value && option.value.trim() !== '') {
                            text.textContent = option.key + '. ' + option.value;
                        } else {
                            text.textContent = option.key + '. (Tidak ada teks)';
                            text.className += ' text-gray-400 italic';
                        }

                wrapper.appendChild(radio);
                wrapper.appendChild(text);
                optionsAreaEl.appendChild(wrapper);
                        
                        // Debug: Log each option being added
                        console.log(`Added option ${option.key}: ${option.value}`);
                    });
                } else {
                    // If no valid options, show error message
                    const message = document.createElement('div');
                    message.className = 'p-4 bg-red-50 border border-red-200 rounded-lg';
                    message.innerHTML = '<p class="text-sm text-red-600 font-medium">Opsi tidak tersedia untuk soal pilihan ganda ini.</p>';
                    optionsAreaEl.appendChild(message);
                }
            } else if (q.type === 'essay' || q.type === 'esai') {
                // Essay type - only show textarea for essay
                const textarea = document.createElement('textarea');
                textarea.className = 'w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent';
                textarea.rows = 6;
                textarea.placeholder = 'Tulis jawaban Anda di sini...';
                textarea.value = answers[q.id] || '';
                textarea.addEventListener('blur', () => {
                    answers[q.id] = textarea.value;
                    saveAnswer(q.id, textarea.value);
                    updateNavBox(index);
                    updateCounts();
                });
                optionsAreaEl.appendChild(textarea);
            } else {
                // Fallback: if type is not recognized
                const message = document.createElement('div');
                message.className = 'p-4 bg-yellow-50 border border-yellow-200 rounded-lg';
                message.innerHTML = `<p class="text-sm text-yellow-800 font-medium">Jenis soal tidak dikenali: "${q.type}"</p>`;
                optionsAreaEl.appendChild(message);
            }

            updateActiveBox(index);
            updatePrevNextState();
            updateBookmarkButton();
        }

        function updateBookmarkButton() {
            const q = questions[currentIndex];
            const isBookmarked = bookmarked[q.id] || false;
            
            if (isBookmarked) {
                bookmarkBtn.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-yellow-400 bg-yellow-50 transition-colors';
                bookmarkIcon.className = 'w-5 h-5 text-yellow-600';
                bookmarkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" fill="currentColor" stroke="none"/>';
                bookmarkBtn.title = 'Hapus tanda soal';
            } else {
                bookmarkBtn.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-gray-300 hover:border-yellow-400 hover:bg-yellow-50 transition-colors';
                bookmarkIcon.className = 'w-5 h-5 text-gray-400';
                bookmarkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>';
                bookmarkBtn.title = 'Tandai soal untuk ditinjau kembali';
            }
        }

        function toggleBookmark() {
            const q = questions[currentIndex];
            if (bookmarked[q.id]) {
                delete bookmarked[q.id];
            } else {
                bookmarked[q.id] = true;
            }
            
            // Save to localStorage
            localStorage.setItem(`exam_${examId}_bookmarks`, JSON.stringify(bookmarked));
            
            updateBookmarkButton();
            updateNavBox(currentIndex);
            updateBookmarkedCount();
        }

        function updateBookmarkedCount() {
            const count = Object.keys(bookmarked).filter(qId => bookmarked[qId]).length;
            bookmarkedCountEl.textContent = count;
        }

        function saveAnswer(questionId, answer) {
            fetch(`{{ url('/siswa/ujian') }}/${examId}/save-answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    question_id: questionId,
                    answer: answer
                })
            }).catch(err => console.error('Error saving answer:', err));
        }

        function updatePrevNextState() {
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= totalQuestions - 1;
            prevBtn.className = 'px-6 py-2.5 rounded-lg font-medium transition-colors ' + 
                (prevBtn.disabled ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-300 text-gray-900 hover:bg-gray-400');
            nextBtn.className = 'px-6 py-2.5 rounded-lg font-medium transition-colors ' + 
                (nextBtn.disabled ? 'bg-primary/60 text-white cursor-not-allowed' : 'bg-primary text-white hover:bg-primary/90');
        }

        function buildNavGrid() {
            navGridEl.innerHTML = '';
            for (let i = 0; i < totalQuestions; i++) {
                const btn = document.createElement('button');
                btn.textContent = i + 1;
                btn.className = 'w-12 h-12 rounded-lg flex items-center justify-center text-sm font-semibold border-2 transition-all';
                btn.addEventListener('click', () => {
                    currentIndex = i;
                    renderQuestion(currentIndex);
                });
                navGridEl.appendChild(btn);
                updateNavBox(i);
            }
            updateActiveBox(0);
        }

        function updateNavBox(i) {
            if (i >= navGridEl.children.length) return;
            
            const btn = navGridEl.children[i];
            const q = questions[i];
            const isAnswered = answers[q.id] && answers[q.id].trim() !== '';
            const isBookmarked = bookmarked[q.id] || false;
            const isActive = i === currentIndex;
            
            let className = 'w-12 h-12 rounded-lg flex items-center justify-center text-sm font-semibold border-2 transition-all relative';
            
            if (isActive) {
                className += ' bg-primary text-white border-primary shadow-md';
            } else if (isAnswered && isBookmarked) {
                className += ' bg-green-500 text-white border-green-600 hover:bg-green-600';
            } else if (isAnswered) {
                className += ' bg-green-500 text-white border-green-600 hover:bg-green-600';
            } else if (isBookmarked) {
                className += ' bg-yellow-100 text-yellow-900 border-yellow-400 hover:bg-yellow-200';
            } else {
                className += ' bg-white text-gray-700 border-gray-300 hover:border-gray-400 hover:bg-gray-50';
            }
            
            btn.className = className;
            
            // Add bookmark indicator (small star icon) if bookmarked
            if (isBookmarked && !isActive) {
                // Check if star already exists
                let star = btn.querySelector('.bookmark-star');
                if (!star) {
                    star = document.createElement('div');
                    star.className = 'bookmark-star absolute top-0 right-0 w-3 h-3';
                    star.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-yellow-500"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>';
                    btn.appendChild(star);
                }
            } else {
                // Remove star if exists
                const star = btn.querySelector('.bookmark-star');
                if (star) {
                    star.remove();
                }
            }
        }

        function updateActiveBox(i) {
            for (let idx = 0; idx < navGridEl.children.length; idx++) {
                updateNavBox(idx);
            }
        }

        function updateCounts() {
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            const unanswered = totalQuestions - answered;
            answeredCountEl.textContent = answered;
            unansweredCountEl.textContent = unanswered;
            updateBookmarkedCount();
        }

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                renderQuestion(currentIndex);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < totalQuestions - 1) {
                currentIndex++;
                renderQuestion(currentIndex);
            }
        });

        bookmarkBtn.addEventListener('click', toggleBookmark);

        finishBtn.addEventListener('click', () => {
            showFinishConfirmModal();
        });
        
        function showFinishConfirmModal() {
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            const unanswered = totalQuestions - answered;
            const bookmarkedCount = Object.keys(bookmarked).filter(qId => bookmarked[qId]).length;
            
            // Update modal content
            document.getElementById('modalTotalQuestions').textContent = totalQuestions;
            document.getElementById('modalAnswered').textContent = answered;
            document.getElementById('modalUnanswered').textContent = unanswered;
            document.getElementById('modalBookmarked').textContent = bookmarkedCount;
            
            // Show warning if there are unanswered questions
            const warningEl = document.getElementById('modalWarning');
            if (unanswered > 0) {
                warningEl.classList.remove('hidden');
                warningEl.innerHTML = `<div class="flex items-center gap-2 text-yellow-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span class="font-medium">Masih ada ${unanswered} soal yang belum dikerjakan.</span></div>`;
            } else {
                warningEl.classList.add('hidden');
            }
            
            // Show modal
            document.getElementById('finishConfirmModal').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('finishConfirmModal').classList.remove('opacity-0');
                document.getElementById('finishConfirmModal').classList.add('opacity-100');
                document.getElementById('modalContent').classList.remove('scale-95');
                document.getElementById('modalContent').classList.add('scale-100');
            }, 10);
        }
        
        function hideFinishConfirmModal() {
            document.getElementById('finishConfirmModal').classList.add('opacity-0');
            document.getElementById('modalContent').classList.remove('scale-100');
            document.getElementById('modalContent').classList.add('scale-95');
            setTimeout(() => {
                document.getElementById('finishConfirmModal').classList.add('hidden');
            }, 200);
        }
        
        function confirmFinishExam() {
            hideFinishConfirmModal();
            document.getElementById('submitForm').submit();
        }

        // Timer countdown
        const startedAt = new Date('{{ $examResult->started_at ? $examResult->started_at->toIso8601String() : now()->toIso8601String() }}');
        const durationSeconds = durationMinutes * 60;
        const endTime = new Date(startedAt.getTime() + (durationSeconds * 1000));
        let timerInterval = null;
        let hasSubmitted = false;
        
        function renderTimer() {
            if (hasSubmitted) {
                return; // Stop timer if already submitted
            }
            
            const now = new Date();
            const remaining = Math.max(0, Math.floor((endTime - now) / 1000));
            
            const h = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const m = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            
            timerDisplay.textContent = `${h}:${m}:${s}`;
            
            const timerBox = document.getElementById('timerBox');
            if (remaining <= 300 && remaining > 0) { // last 5 minutes
                timerBox.className = 'flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg shadow border-2 border-red-800 animate-pulse';
            } else if (remaining <= 600 && remaining > 0) { // last 10 minutes
                timerBox.className = 'flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg shadow border-2 border-orange-800';
            } else if (remaining > 600) {
                timerBox.className = 'flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg shadow border-2 border-red-600';
            }
            
            if (remaining <= 0 && !hasSubmitted) {
                hasSubmitted = true;
                if (timerInterval) {
                    clearInterval(timerInterval);
                }
                
                // Save all answers before submitting
                Object.keys(answers).forEach(questionId => {
                    if (answers[questionId]) {
                        saveAnswer(questionId, answers[questionId]);
                    }
                });
                
                // Small delay to ensure answers are saved
                setTimeout(() => {
                    document.getElementById('submitForm').submit();
                }, 500);
            }
        }
        
        timerInterval = setInterval(renderTimer, 1000);
        renderTimer();

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Prevent shortcuts when typing in textarea
            if (e.target.tagName === 'TEXTAREA') return;
            
            // Arrow keys for navigation
            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                currentIndex--;
                renderQuestion(currentIndex);
            } else if (e.key === 'ArrowRight' && currentIndex < totalQuestions - 1) {
                currentIndex++;
                renderQuestion(currentIndex);
            }
            // Space or B key for bookmark
            else if ((e.key === ' ' || e.key === 'b' || e.key === 'B') && e.target.tagName !== 'BUTTON') {
                e.preventDefault();
                toggleBookmark();
            }
        });

        // Initialize
        buildNavGrid();
        renderQuestion(0);
        updateCounts();
        updateBookmarkedCount();
    </script>
</body>
</html>
