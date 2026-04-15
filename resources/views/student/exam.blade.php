<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Ujian: {{ $exam->title }}</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
            overflow-y: auto;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f3f4f6;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        body {
            overscroll-behavior-y: contain;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        .exam-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }

        button, .no-select {
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        #securityWarning {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(220, 38, 38, 0.95);
            color: white;
            text-align: center;
            padding: 12px;
            font-size: 13px;
            font-weight: bold;
            z-index: 10000;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
        }

        #securityWarning.show {
            transform: translateY(0);
        }

        #blockOverlay, #resetOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.95);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
            padding: 20px;
        }

        #resetOverlay {
            z-index: 99998;
            background: rgba(0,0,0,0.9);
        }

        .top-security-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #dc2626;
            color: white;
            text-align: center;
            padding: 5px;
            font-size: 10px;
            z-index: 10001;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .reset-progress {
            width: 80%;
            max-width: 300px;
            height: 4px;
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
            margin-top: 20px;
            overflow: hidden;
        }

        .reset-progress-bar {
            width: 0%;
            height: 100%;
            background: #4f46e5;
            border-radius: 2px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="top-security-bar" id="securityBar">
        🔒 UJIAN TERKUNCI - JANGAN KELUAR APLIKASI 🔒
    </div>

    <div id="securityWarning">
        ⚠️ DILARANG KELUAR DARI APLIKASI UJIAN!
    </div>

    <div id="blockOverlay">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h2 style="margin: 20px 0 10px; font-size: 24px;">🚫 AKSES DITOLAK!</h2>
        <p style="font-size: 16px; margin-bottom: 10px;">Anda tidak diizinkan keluar dari ujian!</p>
        <button onclick="forceFullReset()" style="margin-top: 30px; padding: 12px 40px; background: #4f46e5; border: none; border-radius: 10px; color: white; font-weight: bold; font-size: 16px;">Kembali ke Ujian</button>
    </div>

    <div id="resetOverlay">
        <div class="spinner"></div>
        <h2 id="resetTitle" style="margin-bottom: 10px;">🔄 MERESET UJIAN...</h2>
        <p id="resetMessage">Terjadi pelanggaran keamanan. Ujian akan dimulai dari awal.</p>
        <div class="reset-progress">
            <div class="reset-progress-bar" id="resetProgressBar"></div>
        </div>
        <p style="font-size: 14px; margin-top: 20px; color: #ffcccc;">Mohon jangan tinggalkan aplikasi ujian!</p>
    </div>

    <div class="exam-container" id="examContainer">
        <header class="sticky top-0 z-50 bg-white shadow-md" style="margin-top: 25px;">
            <div class="flex items-center justify-between px-4 py-3 mx-auto max-w-7xl">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">{{ $exam->title }}</h1>
                    <p class="text-sm text-gray-600">{{ $exam->subject->name ?? '-' }} | Durasi: {{ $exam->duration }} menit</p>
                </div>
                <div id="timerBox" class="flex items-center gap-2 px-4 py-2 text-white bg-orange-600 border-2 border-red-600 rounded-lg shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        <path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zm0 2.25a7.5 7.5 0 110 15 7.5 7.5 0 010-15z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-mono text-lg font-semibold" id="timerDisplay">00:00:00</span>
                </div>
            </div>
        </header>

        <div class="px-4 py-6 mx-auto max-w-7xl">
            <div class="flex flex-col gap-6 lg:flex-row">
                <div class="flex-1 lg:w-2/3">
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <div id="questionArea" class="space-y-6">
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <div class="flex items-center flex-1 gap-3">
                                    <span class="inline-flex items-center justify-center w-10 h-10 text-lg font-bold text-white rounded-lg bg-primary" id="currentNumber">1</span>
                                    <h2 class="flex-1 text-xl font-semibold text-gray-900" id="questionText">Memuat soal...</h2>
                                </div>
                                <button id="bookmarkBtn" type="button" class="inline-flex items-center justify-center w-10 h-10 transition-colors border-2 border-gray-300 rounded-lg hover:border-yellow-400 hover:bg-yellow-50" title="Tandai soal">
                                    <svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                                    </svg>
                                </button>
                            </div>
                            <div id="optionsArea" class="space-y-3"></div>
                            <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-200">
                                <button id="prevBtn" class="px-6 py-2.5 rounded-lg bg-gray-300 text-gray-900 hover:bg-gray-400 transition-colors font-medium">Soal Sebelumnya</button>
                                <button id="nextBtn" class="px-6 py-2.5 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors font-medium">Soal Selanjutnya</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/3">
                    <div class="sticky p-6 bg-white rounded-lg shadow-lg top-24">
                        <h3 class="mb-4 text-lg font-bold text-gray-900">Navigasi Soal</h3>
                        <div class="p-3 mb-4 rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between mb-2 text-sm">
                                <span class="text-gray-600">Total Soal:</span>
                                <span class="font-semibold text-gray-900" id="totalQuestions">0</span>
                            </div>
                            <div class="flex items-center justify-between mb-2 text-sm">
                                <span class="text-green-600">Sudah Dikerjakan:</span>
                                <span class="font-semibold text-green-700" id="answeredCount">0</span>
                            </div>
                            <div class="flex items-center justify-between mb-2 text-sm">
                                <span class="text-yellow-600">Ditandai:</span>
                                <span class="font-semibold text-yellow-700" id="bookmarkedCount">0</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Belum Dikerjakan:</span>
                                <span class="font-semibold text-gray-700" id="unansweredCount">0</span>
                            </div>
                        </div>
                        <div id="navGrid" class="grid grid-cols-5 gap-2 mb-6 overflow-y-auto max-h-96"></div>
                        <div class="mt-auto">
                            <form id="submitForm" method="POST" action="{{ route('siswa.exam.submit', $exam->id) }}">
                                @csrf
                                <input type="hidden" name="question_order" id="questionOrderInput" value="">
                                <button type="button" id="finishBtn" class="w-full px-4 py-3 font-semibold text-white transition-colors bg-red-600 rounded-lg shadow-md hover:bg-red-700">
                                    Selesai Ujian
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============ KONSTANTA ============
        const EXAM_ID = {{ $exam->id }};
        const DURATION_MINUTES = {{ $exam->duration }};
        let originalQuestions = @json($questionsData);

        // ============ VARIABEL GLOBAL ============
        let isSubmittingExam = false;
        let hasFinishedExam = localStorage.getItem(`exam_${EXAM_ID}_finished`) === 'true';
        let isResetting = false;
        let resetCompleted = false;

        // Variabel ujian
        let questions = [];
        let currentIndex = 0;
        let answers = {};
        let bookmarked = {};
        let questionOrder = [];

        // Timer
        let timerInterval = null;
        let startTime = null;

        // ============ FUNGSI ACAK SOAL (SHUFFLE) ============
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        function randomizeQuestions() {
            // Buat salinan soal asli
            let shuffled = [...originalQuestions];

            // Acak urutan soal
            shuffled = shuffleArray(shuffled);

            // Untuk setiap soal pilihan ganda, acak juga opsi jawabannya
            shuffled = shuffled.map(question => {
                if (question.type === 'pilihan_ganda' && question.options) {
                    let optionsArray = [];

                    // Konversi options ke array
                    if (Array.isArray(question.options)) {
                        optionsArray = [...question.options];
                    } else if (typeof question.options === 'object') {
                        optionsArray = Object.values(question.options);
                    }

                    // Acak opsi
                    const shuffledOptions = shuffleArray([...optionsArray]);

                    // Konversi kembali ke format yang diinginkan
                    const newOptions = {};
                    const keys = ['A', 'B', 'C', 'D'];
                    shuffledOptions.slice(0, 4).forEach((opt, idx) => {
                        if (keys[idx]) {
                            newOptions[keys[idx]] = opt;
                        }
                    });

                    return { ...question, options: newOptions };
                }
                return question;
            });

            return shuffled;
        }

        // ============ RESET TOTAL + ACAK SOAL ============
        function updateResetProgress(percent) {
            const progressBar = document.getElementById('resetProgressBar');
            if (progressBar) progressBar.style.width = percent + '%';
        }

        function showResetOverlay(message, title = '🔄 MERESET UJIAN...') {
            const overlay = document.getElementById('resetOverlay');
            const titleEl = document.getElementById('resetTitle');
            const msgEl = document.getElementById('resetMessage');
            if (titleEl) titleEl.innerHTML = title;
            if (msgEl) msgEl.innerHTML = message || 'Terjadi pelanggaran keamanan. Ujian akan dimulai dari awal.';
            if (overlay) overlay.style.display = 'flex';
            updateResetProgress(0);
        }

        function hideResetOverlay() {
            const overlay = document.getElementById('resetOverlay');
            if (overlay) overlay.style.display = 'none';
        }

        async function forceFullReset() {
            if (isResetting || resetCompleted) return;
            isResetting = true;

            console.log('🔄 MEMULAI RESET TOTAL + ACAK SOAL...');
            showResetOverlay('Menghapus semua data ujian...', '🔄 MERESET UJIAN...');
            updateResetProgress(10);

            // 1. HAPUS SEMUA LOCALSTORAGE
            await new Promise(r => setTimeout(r, 100));
            updateResetProgress(20);

            const keysToRemove = [
                `exam_${EXAM_ID}_answers`,
                `exam_${EXAM_ID}_bookmarks`,
                `exam_${EXAM_ID}_finished`,
                `exam_${EXAM_ID}_force_exit`,
                `exam_${EXAM_ID}_exit_time`,
                `exam_${EXAM_ID}_exit_count`,
                `exam_${EXAM_ID}_minimize_time`,
                `exam_${EXAM_ID}_minimize_count`,
                `exam_${EXAM_ID}_temp_answers`,
                `exam_${EXAM_ID}_question_order`,
                `exam_${EXAM_ID}_start_time`
            ];

            keysToRemove.forEach(key => localStorage.removeItem(key));
            updateResetProgress(40);

            // 2. RESET VARIABEL JS
            answers = {};
            bookmarked = {};
            currentIndex = 0;
            updateResetProgress(50);

            // 3. KIRIM RESET KE SERVER (HAPUS SEMUA JAWABAN DI DATABASE)
            try {
                const response = await fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/full-reset`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        reason: 'force_exit_reset',
                        timestamp: new Date().toISOString()
                    })
                });
                console.log('Server reset response:', response.status);
            } catch(e) {
                console.error('Server reset error:', e);
            }
            updateResetProgress(70);

            // 4. ACAK SOAL BARU
            await new Promise(r => setTimeout(r, 200));
            questions = randomizeQuestions();

            // Simpan urutan soal ke localStorage dan hidden input
            questionOrder = questions.map(q => q.id);
            localStorage.setItem(`exam_${EXAM_ID}_question_order`, JSON.stringify(questionOrder));
            const orderInput = document.getElementById('questionOrderInput');
            if (orderInput) orderInput.value = JSON.stringify(questionOrder);

            updateResetProgress(90);

            // 5. RESET TIMER
            if (timerInterval) clearInterval(timerInterval);
            startTime = new Date();
            localStorage.setItem(`exam_${EXAM_ID}_start_time`, startTime.toISOString());

            updateResetProgress(100);
            await new Promise(r => setTimeout(r, 500));

            // 6. RELOAD DENGAN FORCE (Membersihkan cache)
            resetCompleted = true;
            console.log('✅ Reset selesai, me-load ulang halaman dengan soal baru...');

            // Gunakan replace untuk menghindari back button
            window.location.replace(window.location.href.split('?')[0] + '?reset=' + Date.now() + '&shuffled=1');
        }

        // ============ DETEKSI KELUAR PAKSA ============
        let exitCount = parseInt(localStorage.getItem(`exam_${EXAM_ID}_exit_count`) || '0');

        document.addEventListener('visibilitychange', function() {
            if (document.hidden && !isSubmittingExam && !hasFinishedExam && !isResetting && !resetCompleted) {
                exitCount++;
                localStorage.setItem(`exam_${EXAM_ID}_exit_count`, exitCount);
                localStorage.setItem(`exam_${EXAM_ID}_force_exit`, 'true');
                localStorage.setItem(`exam_${EXAM_ID}_exit_time`, new Date().toISOString());

                console.log(`⚠️ Aplikasi ditinggalkan (ke-${exitCount})`);

                fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/force-exit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ exit_count: exitCount })
                }).catch(() => {});
            }
        });

        // CEK SAAT LOAD - APAKAH PERLU RESET?
        window.addEventListener('load', function() {
            const forceExit = localStorage.getItem(`exam_${EXAM_ID}_force_exit`);
            const urlParams = new URLSearchParams(window.location.search);
            const isShuffled = urlParams.get('shuffled');

            console.log('🔍 CEK STATUS:', { forceExit, hasFinishedExam, isShuffled });

            // Cek apakah perlu mengacak soal
            const savedOrder = localStorage.getItem(`exam_${EXAM_ID}_question_order`);

            if (forceExit === 'true' && !hasFinishedExam && !isResetting && !resetCompleted) {
                console.log('🚨 DETEKSI KELUAR PAKSA! Memulai reset...');
                const warning = document.getElementById('securityWarning');
                if (warning) {
                    warning.textContent = '⚠️ TERDETEKSI KELUAR DARI UJIAN! UJIAN AKAN DI-RESET DENGAN SOAL BARU!';
                    warning.classList.add('show');
                }
                forceFullReset();
                return;
            }

            // Jika sudah di-reset dengan shuffle, gunakan soal yang sudah diacak
            if (isShuffled === '1' && savedOrder) {
                try {
                    const order = JSON.parse(savedOrder);
                    // Urutkan soal berdasarkan order yang tersimpan
                    questions = [...originalQuestions].sort((a, b) => {
                        return order.indexOf(a.id) - order.indexOf(b.id);
                    });
                    console.log('✅ Menggunakan soal yang sudah diacak sebelumnya');
                } catch(e) {
                    questions = randomizeQuestions();
                }
            } else if (!savedOrder || forceExit === 'true') {
                // First time atau setelah reset, acak soal
                questions = randomizeQuestions();
                questionOrder = questions.map(q => q.id);
                localStorage.setItem(`exam_${EXAM_ID}_question_order`, JSON.stringify(questionOrder));
                const orderInput = document.getElementById('questionOrderInput');
                if (orderInput) orderInput.value = JSON.stringify(questionOrder);
            } else {
                // Normal load, gunakan soal yang sudah ada
                try {
                    const order = JSON.parse(savedOrder);
                    questions = [...originalQuestions].sort((a, b) => {
                        return order.indexOf(a.id) - order.indexOf(b.id);
                    });
                } catch(e) {
                    questions = originalQuestions;
                }
            }

            // Bersihkan flag force_exit
            localStorage.removeItem(`exam_${EXAM_ID}_force_exit`);

            // Load saved answers (jika ada dan tidak dalam mode reset)
            const savedAnswersJson = localStorage.getItem(`exam_${EXAM_ID}_answers`);
            if (savedAnswersJson && forceExit !== 'true') {
                try {
                    const saved = JSON.parse(savedAnswersJson);
                    Object.keys(saved).forEach(key => {
                        if (saved[key] && saved[key].trim() !== '') {
                            answers[key] = saved[key];
                        }
                    });
                } catch(e) {}
            }

            // Load bookmarks
            const savedBookmarks = localStorage.getItem(`exam_${EXAM_ID}_bookmarks`);
            if (savedBookmarks && forceExit !== 'true') {
                try {
                    const bookmarksData = JSON.parse(savedBookmarks);
                    Object.keys(bookmarksData).forEach(qId => {
                        if (bookmarksData[qId]) bookmarked[qId] = true;
                    });
                } catch(e) {}
            }

            // Inisialisasi UI
            document.getElementById('totalQuestions').textContent = questions.length;
            buildNavGrid();
            renderQuestion(0);
            updateCounts();
            updateBookmarkedCount();
            startTimer();

            console.log('✅ Ujian siap dengan', questions.length, 'soal (urutan sudah diacak)');
        });

        // ============ FUNGSI UJIAN ============
        function renderQuestion(index) {
            if (index < 0 || index >= questions.length) return;

            const q = questions[index];
            document.getElementById('currentNumber').textContent = index + 1;
            document.getElementById('questionText').innerHTML = q.text || 'Memuat soal...';
            const optionsArea = document.getElementById('optionsArea');
            optionsArea.innerHTML = '';

            if (q.type === 'pilihan_ganda') {
                let optionsList = [];

                if (q.options) {
                    if (typeof q.options === 'object') {
                        const keys = ['A', 'B', 'C', 'D'];
                        keys.forEach(key => {
                            if (q.options[key] !== undefined && q.options[key] !== null) {
                                let value = String(q.options[key] || '').trim();
                                if (value !== 'null' && value !== 'undefined' && value !== '') {
                                    optionsList.push({ key: key, value: value });
                                }
                            }
                        });
                    }
                }

                if (optionsList.length === 0) {
                    optionsList = [
                        { key: 'A', value: 'Pilihan A' },
                        { key: 'B', value: 'Pilihan B' },
                        { key: 'C', value: 'Pilihan C' },
                        { key: 'D', value: 'Pilihan D' }
                    ];
                }

                optionsList.forEach((option) => {
                    const wrapper = document.createElement('label');
                    wrapper.className = 'flex items-center gap-3 px-4 py-3 rounded-lg border-2 border-gray-200 hover:border-primary hover:bg-primary/5 cursor-pointer transition-all';

                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = `q_${q.id}`;
                    radio.value = option.key;
                    radio.checked = answers[q.id] === option.key;
                    radio.addEventListener('change', () => {
                        answers[q.id] = option.key;
                        saveAnswerToLocal(q.id, option.key);
                        updateNavBox(index);
                        updateCounts();
                    });

                    const text = document.createElement('span');
                    text.className = 'flex-1 text-gray-900';
                    text.textContent = option.key + '. ' + option.value;

                    wrapper.appendChild(radio);
                    wrapper.appendChild(text);
                    optionsArea.appendChild(wrapper);
                });
            } else if (q.type === 'essay' || q.type === 'esai') {
                const textarea = document.createElement('textarea');
                textarea.className = 'w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent';
                textarea.rows = 6;
                textarea.placeholder = 'Tulis jawaban Anda di sini...';
                textarea.value = answers[q.id] || '';
                textarea.addEventListener('input', () => {
                    answers[q.id] = textarea.value;
                    saveAnswerToLocal(q.id, textarea.value);
                    updateNavBox(index);
                    updateCounts();
                });
                optionsArea.appendChild(textarea);
            }

            updateActiveBox(index);
            updatePrevNextState();
            updateBookmarkButton();
        }

        function saveAnswerToLocal(questionId, answer) {
            // Simpan ke localStorage
            const allAnswers = JSON.parse(localStorage.getItem(`exam_${EXAM_ID}_answers`) || '{}');
            allAnswers[questionId] = answer;
            localStorage.setItem(`exam_${EXAM_ID}_answers`, JSON.stringify(allAnswers));

            // Kirim ke server (async, tidak perlu menunggu)
            fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/save-answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ question_id: questionId, answer: answer })
            }).catch(() => {});
        }

        function updateBookmarkButton() {
            const q = questions[currentIndex];
            const isBookmarked = bookmarked[q.id] || false;
            const btn = document.getElementById('bookmarkBtn');
            const icon = document.getElementById('bookmarkIcon');

            if (isBookmarked) {
                btn.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-yellow-400 bg-yellow-50 transition-colors';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" fill="currentColor" stroke="none"/>';
            } else {
                btn.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-gray-300 hover:border-yellow-400 hover:bg-yellow-50 transition-colors';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>';
            }
        }

        function toggleBookmark() {
            const q = questions[currentIndex];
            if (bookmarked[q.id]) {
                delete bookmarked[q.id];
            } else {
                bookmarked[q.id] = true;
            }
            localStorage.setItem(`exam_${EXAM_ID}_bookmarks`, JSON.stringify(bookmarked));
            updateBookmarkButton();
            updateNavBox(currentIndex);
            updateBookmarkedCount();
        }

        function updateBookmarkedCount() {
            const count = Object.keys(bookmarked).filter(qId => bookmarked[qId]).length;
            document.getElementById('bookmarkedCount').textContent = count;
        }

        function updatePrevNextState() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= questions.length - 1;
            prevBtn.className = 'px-6 py-2.5 rounded-lg font-medium transition-colors ' +
                (prevBtn.disabled ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-300 text-gray-900 hover:bg-gray-400');
            nextBtn.className = 'px-6 py-2.5 rounded-lg font-medium transition-colors ' +
                (nextBtn.disabled ? 'bg-primary/60 text-white cursor-not-allowed' : 'bg-primary text-white hover:bg-primary/90');
        }

        function buildNavGrid() {
            const navGrid = document.getElementById('navGrid');
            navGrid.innerHTML = '';
            for (let i = 0; i < questions.length; i++) {
                const btn = document.createElement('button');
                btn.textContent = i + 1;
                btn.className = 'w-12 h-12 rounded-lg flex items-center justify-center text-sm font-semibold border-2 transition-all';
                btn.addEventListener('click', () => {
                    currentIndex = i;
                    renderQuestion(currentIndex);
                });
                navGrid.appendChild(btn);
                updateNavBox(i);
            }
        }

        function updateNavBox(i) {
            const navGrid = document.getElementById('navGrid');
            if (i >= navGrid.children.length) return;

            const btn = navGrid.children[i];
            const q = questions[i];
            const isAnswered = answers[q.id] && answers[q.id].trim() !== '';
            const isBookmarked = bookmarked[q.id] || false;
            const isActive = i === currentIndex;

            let className = 'w-12 h-12 rounded-lg flex items-center justify-center text-sm font-semibold border-2 transition-all';

            if (isActive) {
                className += ' bg-primary text-white border-primary shadow-md';
            } else if (isAnswered && isBookmarked) {
                className += ' bg-green-500 text-white border-green-600';
            } else if (isAnswered) {
                className += ' bg-green-500 text-white border-green-600';
            } else if (isBookmarked) {
                className += ' bg-yellow-100 text-yellow-900 border-yellow-400';
            } else {
                className += ' bg-white text-gray-700 border-gray-300';
            }

            btn.className = className;
        }

        function updateActiveBox(i) {
            const navGrid = document.getElementById('navGrid');
            for (let idx = 0; idx < navGrid.children.length; idx++) {
                updateNavBox(idx);
            }
        }

        function updateCounts() {
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            const unanswered = questions.length - answered;
            document.getElementById('answeredCount').textContent = answered;
            document.getElementById('unansweredCount').textContent = unanswered;
        }

        // ============ TIMER ============
        function startTimer() {
            const savedStartTime = localStorage.getItem(`exam_${EXAM_ID}_start_time`);
            if (savedStartTime && !hasFinishedExam) {
                startTime = new Date(savedStartTime);
            } else {
                startTime = new Date();
                localStorage.setItem(`exam_${EXAM_ID}_start_time`, startTime.toISOString());
            }

            const endTime = new Date(startTime.getTime() + (DURATION_MINUTES * 60 * 1000));

            function updateTimer() {
                if (hasFinishedExam || isSubmittingExam) return;

                const now = new Date();
                const remaining = Math.max(0, Math.floor((endTime - now) / 1000));
                const h = String(Math.floor(remaining / 3600)).padStart(2, '0');
                const m = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
                const s = String(remaining % 60).padStart(2, '0');
                document.getElementById('timerDisplay').textContent = `${h}:${m}:${s}`;

                if (remaining <= 0) {
                    clearInterval(timerInterval);
                    alert('Waktu habis! Ujian akan diselesaikan.');
                    document.getElementById('submitForm').submit();
                }
            }

            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(updateTimer, 1000);
            updateTimer();
        }

        // ============ EVENT LISTENERS ============
        document.getElementById('prevBtn').addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                renderQuestion(currentIndex);
            }
        });

        document.getElementById('nextBtn').addEventListener('click', () => {
            if (currentIndex < questions.length - 1) {
                currentIndex++;
                renderQuestion(currentIndex);
            }
        });

        document.getElementById('bookmarkBtn').addEventListener('click', toggleBookmark);

        document.getElementById('finishBtn').addEventListener('click', () => {
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            if (confirm(`Anda akan menyelesaikan ujian.\n\nSoal terjawab: ${answered}/${questions.length}\n\nYakin ingin selesai?`)) {
                isSubmittingExam = true;
                hasFinishedExam = true;
                localStorage.setItem(`exam_${EXAM_ID}_finished`, 'true');
                localStorage.removeItem(`exam_${EXAM_ID}_force_exit`);
                document.getElementById('submitForm').submit();
            }
        });

        // ============ KEAMANAN ============
        function showSecurityWarning(message) {
            const warning = document.getElementById('securityWarning');
            warning.textContent = message;
            warning.classList.add('show');
            setTimeout(() => warning.classList.remove('show'), 3000);
        }

        // Cegah tombol back
        (function() {
            for(let i = 0; i < 100; i++) history.pushState(null, null, location.href);
            window.addEventListener('popstate', function(e) {
                if (!isSubmittingExam && !hasFinishedExam && !isResetting) {
                    e.preventDefault();
                    showSecurityWarning('🚫 Tombol BACK DINONAKTIFKAN!');
                    document.getElementById('blockOverlay').style.display = 'flex';
                    for(let i = 0; i < 100; i++) history.pushState(null, null, location.href);
                }
            });
        })();

        // Cegah gesture back
        let touchStart = 0;
        document.addEventListener('touchstart', e => { touchStart = e.touches[0].clientX; });
        document.addEventListener('touchend', e => {
            if (touchStart < 50 && e.changedTouches[0].clientX > 100 && !isSubmittingExam && !hasFinishedExam) {
                showSecurityWarning('🚫 Gesture kembali DINONAKTIFKAN!');
                document.getElementById('blockOverlay').style.display = 'flex';
            }
        });

        // Cegah refresh
        window.addEventListener('beforeunload', e => {
            if (!isSubmittingExam && !hasFinishedExam && !isResetting) {
                e.preventDefault();
                e.returnValue = '⚠️ UJIAN SEDANG BERLANGSUNG!';
            }
        });

        // Override untuk Kodular
        if (window.Android) {
            window.Android.onBackPressed = () => {
                if (!isSubmittingExam && !hasFinishedExam) {
                    showSecurityWarning('🚫 Tombol back dinonaktifkan!');
                    document.getElementById('blockOverlay').style.display = 'flex';
                    return true;
                }
                return false;
            };
        }

        console.log('✅ Mode keamanan dengan RESET TOTAL + ACAK SOAL aktif');
    </script>
</body>
</html>
