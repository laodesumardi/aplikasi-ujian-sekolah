<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Ujian: {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* RESET TOTAL - TANPA SATUPUN ELEMEN YANG TERLIHAT SELAIN UJIAN */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* HILANGKAN SEMUA ELEMEN YANG TIDAK PERLU */
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #f3f4f6;
        }

        /* HILANGKAN SCROLLBAR */
        body::-webkit-scrollbar {
            display: none;
        }

        /* CONTAINER UTAMA FULL SCREEN TANPA PADDING/MARGIN */
        .exam-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            background: #f3f4f6;
        }

        /* HEADER LANGSUNG DIBAWAH FULL SCREEN */
        .exam-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 12px 16px;
        }

        /* HILANGKAN SEMUA ELEMEN KEAMANAN YANG TIDAK PERLU */
        #securityWarning, #blockOverlay, #resetOverlay, .top-security-bar,
        #fullscreenNotification, .security-badge {
            display: none !important;
        }

        /* TAMPILAN TIMER YANG MINIMALIS */
        .timer-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ea580c;
            color: white;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: bold;
        }

        /* NAVIGASI GRID YANG RESPONSIF */
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 24px;
            max-height: 400px;
            overflow-y: auto;
        }

        .nav-btn {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-weight: 600;
            border: 2px solid #e5e7eb;
            background: white;
            transition: all 0.2s;
        }

        /* PASTIKAN TIDAK ADA YANG OVERFLOW */
        .max-w-7xl {
            max-width: 1280px;
            margin: 0 auto;
            padding: 16px;
        }
    </style>
</head>
<body>

    <div class="exam-container" id="examContainer">
        <!-- HEADER TANPA ELEMEN TAMBAHAN -->
        <div class="exam-header">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h1 style="font-size: 18px; font-weight: bold; color: #111827;">{{ $exam->title }}</h1>
                    <p style="font-size: 12px; color: #6b7280;">{{ $exam->subject->name ?? '-' }} | {{ $exam->duration }} menit</p>
                </div>
                <div class="timer-box" id="timerBox">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px;">
                        <path d="M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        <path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zm0 2.25a7.5 7.5 0 110 15 7.5 7.5 0 010-15z" clip-rule="evenodd" />
                    </svg>
                    <span id="timerDisplay" style="font-family: monospace; font-size: 18px; font-weight: 600;">00:00:00</span>
                </div>
            </div>
        </div>

        <!-- KONTEN UTAMA -->
        <div class="max-w-7xl">
            <div style="display: flex; flex-direction: column; gap: 24px;">

                <!-- KOLOM SOAL -->
                <div style="flex: 1;">
                    <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                    <span id="currentNumber" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #4f46e5; color: white; font-weight: bold; border-radius: 12px; font-size: 18px;">1</span>
                                    <h2 id="questionText" style="flex: 1; font-size: 18px; font-weight: 600; color: #111827;">Memuat soal...</h2>
                                </div>
                                <button id="bookmarkBtn" type="button" style="width: 40px; height: 40px; border: 2px solid #e5e7eb; border-radius: 12px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                    <svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px; color: #9ca3af;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                                    </svg>
                                </button>
                            </div>
                            <div id="optionsArea" style="display: flex; flex-direction: column; gap: 12px;"></div>
                        </div>

                        <div style="display: flex; justify-content: space-between; padding-top: 20px; margin-top: 20px; border-top: 1px solid #e5e7eb;">
                            <button id="prevBtn" style="padding: 10px 24px; border-radius: 12px; background: #e5e7eb; border: none; font-weight: 500;">Soal Sebelumnya</button>
                            <button id="nextBtn" style="padding: 10px 24px; border-radius: 12px; background: #4f46e5; color: white; border: none; font-weight: 500;">Soal Selanjutnya</button>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR NAVIGASI -->
                <div style="width: 100%;">
                    <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 16px;">Navigasi Soal</h3>

                        <div style="background: #f9fafb; border-radius: 12px; padding: 12px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                                <span style="color: #4b5563;">Total Soal:</span>
                                <span id="totalQuestions" style="font-weight: 600;">{{ $questions->count() }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                                <span style="color: #16a34a;">Sudah Dikerjakan:</span>
                                <span id="answeredCount" style="font-weight: 600; color: #16a34a;">0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                                <span style="color: #ca8a04;">Ditandai:</span>
                                <span id="bookmarkedCount" style="font-weight: 600; color: #ca8a04;">0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px;">
                                <span style="color: #4b5563;">Belum Dikerjakan:</span>
                                <span id="unansweredCount" style="font-weight: 600;">{{ $questions->count() }}</span>
                            </div>
                        </div>

                        <div id="navGrid" class="nav-grid"></div>

                        <form id="submitForm" method="POST" action="{{ route('siswa.exam.submit', $exam->id) }}">
                            @csrf
                            <input type="hidden" name="question_order" id="questionOrderInput">
                            <button type="button" id="finishBtn" style="width: 100%; padding: 12px; background: #dc2626; color: white; border: none; border-radius: 12px; font-weight: 600; margin-top: 16px;">
                                Selesai Ujian
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============ FULL SCREEN FORCE (TANPA ELEMEN TAMBAHAN) ============

        // Paksa full screen saat halaman dimuat
        function forceFullScreen() {
            const docEl = document.documentElement;

            if (docEl.requestFullscreen) {
                docEl.requestFullscreen().catch(err => {
                    console.log('Full screen error:', err);
                });
            } else if (docEl.webkitRequestFullscreen) {
                docEl.webkitRequestFullscreen();
            } else if (docEl.msRequestFullscreen) {
                docEl.msRequestFullscreen();
            }
        }

        // Deteksi jika keluar dari full screen - langsung kembali
        function detectFullScreenExit() {
            if (!document.fullscreenElement && !isSubmittingExam && !hasFinishedExam) {
                setTimeout(() => {
                    forceFullScreen();
                }, 100);
            }
        }

        // Pasang event listener untuk full screen change
        document.addEventListener('fullscreenchange', detectFullScreenExit);
        document.addEventListener('webkitfullscreenchange', detectFullScreenExit);

        // Paksa full screen saat load
        window.addEventListener('load', function() {
            setTimeout(forceFullScreen, 100);

            // Cek setiap 3 detik untuk memastikan tetap full screen
            setInterval(() => {
                if (!document.fullscreenElement && !isSubmittingExam && !hasFinishedExam) {
                    forceFullScreen();
                }
            }, 3000);
        });

        // ============ VARIABEL ============
        let isSubmittingExam = false;
        let hasFinishedExam = localStorage.getItem(`exam_{{ $exam->id }}_finished`) === 'true';
        let isResetting = false;

        const EXAM_ID = {{ $exam->id }};
        let originalQuestions = @json($questionsData);
        let questions = [];
        let currentIndex = 0;
        let answers = {};
        let bookmarked = {};
        let questionOrder = [];

        // ============ FUNGSI ACAK SOAL ============
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        function randomizeQuestions() {
            let shuffled = shuffleArray([...originalQuestions]);

            shuffled = shuffled.map(question => {
                if (question.type === 'pilihan_ganda' && question.options) {
                    let optionsArray = [];

                    if (Array.isArray(question.options)) {
                        optionsArray = [...question.options];
                    } else if (typeof question.options === 'object') {
                        optionsArray = Object.values(question.options);
                    }

                    const shuffledOptions = shuffleArray([...optionsArray]);
                    const newOptions = {};
                    const keys = ['A', 'B', 'C', 'D'];
                    shuffledOptions.slice(0, 4).forEach((opt, idx) => {
                        if (keys[idx]) newOptions[keys[idx]] = opt;
                    });

                    return { ...question, options: newOptions };
                }
                return question;
            });

            return shuffled;
        }

        // ============ RESET TOTAL ============
        async function performFullReset() {
            if (isResetting) return;
            isResetting = true;

            console.log('🔄 RESET TOTAL...');

            // Hapus semua localStorage
            const keysToRemove = [
                `exam_${EXAM_ID}_answers`, `exam_${EXAM_ID}_bookmarks`, `exam_${EXAM_ID}_finished`,
                `exam_${EXAM_ID}_force_exit`, `exam_${EXAM_ID}_exit_time`, `exam_${EXAM_ID}_exit_count`,
                `exam_${EXAM_ID}_minimize_time`, `exam_${EXAM_ID}_minimize_count`, `exam_${EXAM_ID}_temp_answers`,
                `exam_${EXAM_ID}_question_order`, `exam_${EXAM_ID}_start_time`
            ];
            keysToRemove.forEach(key => localStorage.removeItem(key));

            // Reset variabel
            answers = {};
            bookmarked = {};
            currentIndex = 0;

            // Kirim ke server
            try {
                await fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/full-reset`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ reason: 'force_exit_reset' })
                });
            } catch(e) {}

            // Acak soal baru
            questions = randomizeQuestions();
            questionOrder = questions.map(q => q.id);
            localStorage.setItem(`exam_${EXAM_ID}_question_order`, JSON.stringify(questionOrder));
            document.getElementById('questionOrderInput').value = JSON.stringify(questionOrder);

            // Reset timer
            localStorage.setItem(`exam_${EXAM_ID}_start_time`, new Date().toISOString());

            // Force reload
            setTimeout(() => {
                window.location.replace(window.location.href.split('?')[0] + '?reset=' + Date.now());
            }, 500);
        }

        // ============ DETEKSI KELUAR PAKSA ============
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && !isSubmittingExam && !hasFinishedExam && !isResetting) {
                localStorage.setItem(`exam_${EXAM_ID}_force_exit`, 'true');
                fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/force-exit`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).catch(() => {});
            }
        });

        window.addEventListener('load', function() {
            const forceExit = localStorage.getItem(`exam_${EXAM_ID}_force_exit`);
            const savedOrder = localStorage.getItem(`exam_${EXAM_ID}_question_order`);

            if (forceExit === 'true' && !hasFinishedExam && !isResetting) {
                performFullReset();
                return;
            }

            // Load atau acak soal
            if (savedOrder && !forceExit) {
                try {
                    const order = JSON.parse(savedOrder);
                    questions = [...originalQuestions].sort((a, b) => order.indexOf(a.id) - order.indexOf(b.id));
                } catch(e) {
                    questions = randomizeQuestions();
                }
            } else {
                questions = randomizeQuestions();
                questionOrder = questions.map(q => q.id);
                localStorage.setItem(`exam_${EXAM_ID}_question_order`, JSON.stringify(questionOrder));
            }

            localStorage.removeItem(`exam_${EXAM_ID}_force_exit`);

            // Load answers
            const savedAnswers = localStorage.getItem(`exam_${EXAM_ID}_answers`);
            if (savedAnswers) {
                try {
                    Object.assign(answers, JSON.parse(savedAnswers));
                } catch(e) {}
            }

            // Load bookmarks
            const savedBookmarks = localStorage.getItem(`exam_${EXAM_ID}_bookmarks`);
            if (savedBookmarks) {
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

            console.log('✅ Ujian siap dengan', questions.length, 'soal');
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
                if (q.options && typeof q.options === 'object') {
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
                    wrapper.style.cssText = 'display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; border: 2px solid #e5e7eb; cursor: pointer; transition: all 0.2s;';

                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = `q_${q.id}`;
                    radio.value = option.key;
                    radio.checked = answers[q.id] === option.key;
                    radio.style.width = '18px';
                    radio.style.height = '18px';
                    radio.addEventListener('change', () => {
                        answers[q.id] = option.key;
                        saveAnswer(q.id, option.key);
                        updateNavBox(index);
                        updateCounts();
                    });

                    const text = document.createElement('span');
                    text.style.cssText = 'flex: 1; color: #111827;';
                    text.textContent = option.key + '. ' + option.value;

                    wrapper.appendChild(radio);
                    wrapper.appendChild(text);
                    optionsArea.appendChild(wrapper);
                });
            } else if (q.type === 'essay' || q.type === 'esai') {
                const textarea = document.createElement('textarea');
                textarea.style.cssText = 'width: 100%; border: 2px solid #e5e7eb; border-radius: 12px; padding: 12px; font-size: 14px; min-height: 150px;';
                textarea.placeholder = 'Tulis jawaban Anda di sini...';
                textarea.value = answers[q.id] || '';
                textarea.addEventListener('input', () => {
                    answers[q.id] = textarea.value;
                    saveAnswer(q.id, textarea.value);
                    updateNavBox(index);
                    updateCounts();
                });
                optionsArea.appendChild(textarea);
            }

            updateActiveBox(index);
            updatePrevNextState();
            updateBookmarkButton();
        }

        function saveAnswer(questionId, answer) {
            const allAnswers = JSON.parse(localStorage.getItem(`exam_${EXAM_ID}_answers`) || '{}');
            allAnswers[questionId] = answer;
            localStorage.setItem(`exam_${EXAM_ID}_answers`, JSON.stringify(allAnswers));

            fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/save-answer`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ question_id: questionId, answer: answer })
            }).catch(() => {});
        }

        function updateBookmarkButton() {
            const q = questions[currentIndex];
            const isBookmarked = bookmarked[q.id] || false;
            const btn = document.getElementById('bookmarkBtn');
            const icon = document.getElementById('bookmarkIcon');

            if (isBookmarked) {
                btn.style.borderColor = '#facc15';
                btn.style.background = '#fefce8';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" fill="currentColor" stroke="none"/>';
                icon.style.color = '#ca8a04';
            } else {
                btn.style.borderColor = '#e5e7eb';
                btn.style.background = 'transparent';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>';
                icon.style.color = '#9ca3af';
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
            prevBtn.style.opacity = prevBtn.disabled ? '0.5' : '1';
            nextBtn.style.opacity = nextBtn.disabled ? '0.5' : '1';
        }

        function buildNavGrid() {
            const navGrid = document.getElementById('navGrid');
            navGrid.innerHTML = '';
            for (let i = 0; i < questions.length; i++) {
                const btn = document.createElement('button');
                btn.textContent = i + 1;
                btn.className = 'nav-btn';
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

            if (isActive) {
                btn.style.background = '#4f46e5';
                btn.style.color = 'white';
                btn.style.borderColor = '#4f46e5';
            } else if (isAnswered && isBookmarked) {
                btn.style.background = '#22c55e';
                btn.style.color = 'white';
                btn.style.borderColor = '#16a34a';
            } else if (isAnswered) {
                btn.style.background = '#22c55e';
                btn.style.color = 'white';
                btn.style.borderColor = '#16a34a';
            } else if (isBookmarked) {
                btn.style.background = '#fefce8';
                btn.style.color = '#ca8a04';
                btn.style.borderColor = '#facc15';
            } else {
                btn.style.background = 'white';
                btn.style.color = '#374151';
                btn.style.borderColor = '#e5e7eb';
            }
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
            const durationMinutes = {{ $exam->duration }};
            let startTime = localStorage.getItem(`exam_${EXAM_ID}_start_time`);

            if (!startTime || hasFinishedExam) {
                startTime = new Date().toISOString();
                localStorage.setItem(`exam_${EXAM_ID}_start_time`, startTime);
            }

            const endTime = new Date(new Date(startTime).getTime() + (durationMinutes * 60 * 1000));

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

            updateTimer();
            const timerInterval = setInterval(updateTimer, 1000);
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
            if (confirm(`Selesaikan ujian?\n\nTerjawab: ${answered}/${questions.length}`)) {
                isSubmittingExam = true;
                hasFinishedExam = true;
                localStorage.setItem(`exam_${EXAM_ID}_finished`, 'true');
                document.getElementById('submitForm').submit();
            }
        });

        // ============ KEAMANAN ============
        // Cegah tombol back
        (function() {
            for(let i = 0; i < 100; i++) history.pushState(null, null, location.href);
            window.addEventListener('popstate', function(e) {
                if (!isSubmittingExam && !hasFinishedExam && !isResetting) {
                    e.preventDefault();
                    for(let i = 0; i < 100; i++) history.pushState(null, null, location.href);
                }
            });
        })();

        // Cegah gesture back
        let touchStart = 0;
        document.addEventListener('touchstart', e => { touchStart = e.touches[0].clientX; });
        document.addEventListener('touchend', e => {
            if (touchStart < 50 && e.changedTouches[0].clientX > 100 && !isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
            }
        });

        // Cegah refresh
        window.addEventListener('beforeunload', e => {
            if (!isSubmittingExam && !hasFinishedExam && !isResetting) {
                e.preventDefault();
                e.returnValue = 'Ujian sedang berlangsung!';
            }
        });

        // Override untuk Kodular
        if (window.Android) {
            window.Android.onBackPressed = () => {
                if (!isSubmittingExam && !hasFinishedExam) return true;
                return false;
            };
        }

        console.log('✅ Full screen mode aktif - tanpa elemen tambahan');
    </script>
</body>
</html>
