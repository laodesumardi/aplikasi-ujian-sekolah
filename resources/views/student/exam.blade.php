<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Ujian: {{ $exam->title }} - Mode Aman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .start-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
        }

        .start-screen h1 {
            font-size: clamp(24px, 6vw, 42px);
            margin-bottom: 20px;
            font-weight: 800;
        }

        .start-screen p {
            font-size: clamp(14px, 4vw, 18px);
            margin-bottom: 40px;
            opacity: 0.95;
            padding: 0 30px;
            max-width: 500px;
        }

        .btn-start {
            padding: 16px 50px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-start:active {
            transform: scale(0.96);
        }

        .exam-wrapper {
            display: none;
            width: 100%;
            height: 100%;
            background: #f0f2f5;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .exam-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: white;
            padding: 12px 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .timer-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ef4444;
            color: white;
            padding: 8px 18px;
            border-radius: 40px;
            font-weight: bold;
        }

        .timer-display {
            font-family: 'Courier New', monospace;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        @media (min-width: 992px) {
            .main-content {
                flex-direction: row;
                align-items: flex-start;
            }
            .question-section {
                flex: 2;
            }
            .nav-section {
                flex: 1;
                position: sticky;
                top: 80px;
            }
        }

        .question-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #4f46e5;
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 16px;
            font-size: 20px;
            font-weight: bold;
        }

        .question-text {
            flex: 1;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .btn-bookmark {
            width: 44px;
            height: 44px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-bookmark.active {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .options-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 32px;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .option:hover {
            border-color: #4f46e5;
            background: #eef2ff;
        }

        .option input {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .option-text {
            flex: 1;
            font-size: 15px;
            color: #374151;
        }

        .essay-input {
            width: 100%;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            font-size: 15px;
            font-family: inherit;
            resize: vertical;
            min-height: 180px;
        }

        .essay-input:focus {
            outline: none;
            border-color: #4f46e5;
        }

        .question-nav {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .btn-nav {
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }

        .btn-prev {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-next {
            background: #4f46e5;
            color: white;
        }

        .btn-nav:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .nav-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .stats {
            background: #f8fafc;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 20px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 6px 0;
        }

        .stat-label {
            color: #64748b;
        }

        .stat-value {
            font-weight: 700;
        }

        .stat-value.answered { color: #22c55e; }
        .stat-value.bookmarked { color: #f59e0b; }
        .stat-value.unanswered { color: #ef4444; }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 20px;
            max-height: 350px;
            overflow-y: auto;
            padding: 4px;
        }

        .nav-btn {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            border: 2px solid #e5e7eb;
            background: white;
            cursor: pointer;
        }

        .btn-finish {
            width: 100%;
            padding: 14px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 40px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            margin-top: 16px;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            z-index: 9999999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 28px;
            padding: 28px;
            width: 90%;
            max-width: 360px;
            text-align: center;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .modal-buttons button {
            flex: 1;
            padding: 12px;
            border-radius: 40px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .toast-warning {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #ef4444;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            z-index: 9999998;
            transition: transform 0.3s;
            white-space: nowrap;
        }

        .toast-warning.show {
            transform: translateX(-50%) translateY(0);
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>

<div class="start-screen" id="startScreen">
    <h1>📱🔒 {{ $exam->title }}</h1>
    <p>Mode Ujian Super Aman<br>Layar Penuh Total</p>
    <button class="btn-start" id="startBtn">🚀 MULAI UJIAN</button>
    <div style="position: absolute; bottom: 30px; font-size: 12px; opacity: 0.7;">
        ⚠️ Dilarang keluar dari aplikasi selama ujian berlangsung
    </div>
</div>

<div class="exam-wrapper" id="examWrapper">
    <div class="exam-header">
        <div class="header-inner">
            <div>
                <h2 style="font-size: 18px; font-weight: 700;">{{ $exam->title }}</h2>
                <p style="font-size: 12px; color: #6b7280;">{{ $exam->subject->name ?? 'Ujian' }} | Durasi: {{ $exam->duration }} menit</p>
            </div>
            <div class="timer-box" id="timerBox">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                    <path d="M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    <path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zm0 2.25a7.5 7.5 0 110 15 7.5 7.5 0 010-15z" clip-rule="evenodd" />
                </svg>
                <span class="timer-display" id="timerDisplay">00:00:00</span>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="question-section">
            <div class="question-card">
                <div class="question-header">
                    <span class="question-number" id="currentNumber">1</span>
                    <span class="question-text" id="questionText">Memuat soal...</span>
                    <button class="btn-bookmark" id="bookmarkBtn" title="Tandai soal">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                        </svg>
                    </button>
                </div>
                <div id="optionsArea" class="options-container"></div>
                <div class="question-nav">
                    <button class="btn-nav btn-prev" id="prevBtn">◀ Sebelumnya</button>
                    <button class="btn-nav btn-next" id="nextBtn">Selanjutnya ▶</button>
                </div>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-card">
                <h3 style="margin-bottom: 16px; font-size: 16px;">📋 Navigasi Soal</h3>
                <div class="stats">
                    <div class="stat-item"><span class="stat-label">📊 Total Soal</span><span class="stat-value" id="totalQuestions">{{ $questions->count() }}</span></div>
                    <div class="stat-item"><span class="stat-label">✅ Sudah Dikerjakan</span><span class="stat-value answered" id="answeredCount">0</span></div>
                    <div class="stat-item"><span class="stat-label">⭐ Ditandai</span><span class="stat-value bookmarked" id="bookmarkedCount">0</span></div>
                    <div class="stat-item"><span class="stat-label">⏳ Belum Dikerjakan</span><span class="stat-value unanswered" id="unansweredCount">{{ $questions->count() }}</span></div>
                </div>
                <div id="navGrid" class="nav-grid"></div>
                <form id="submitForm" method="POST" action="{{ route('siswa.exam.submit', $exam->id) }}">
                    @csrf
                    <input type="hidden" name="question_order" id="questionOrderInput">
                    <button type="button" class="btn-finish" id="finishBtn">✅ Selesai Ujian</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="finishModal">
    <div class="modal-content">
        <h3>✅ Selesaikan Ujian?</h3>
        <p id="modalMessage" style="margin-bottom: 20px; color: #6b7280;"></p>
        <div class="modal-buttons">
            <button style="background: #e5e7eb;" id="modalCancelBtn">Batal</button>
            <button style="background: #ef4444; color: white;" id="modalConfirmBtn">Ya, Selesai</button>
        </div>
    </div>
</div>

<div class="toast-warning" id="toastWarning">⚠️ Dilarang keluar dari ujian!</div>

<script>
    (function() {
        // ==================== VARIABEL GLOBAL ====================
        let examActive = false;
        let isSubmitting = false;
        let isFinished = localStorage.getItem(`exam_{{ $exam->id }}_finished`) === 'true';
        let timerInterval = null;
        let fullScreenInterval = null;

        // Data ujian
        const EXAM_ID = {{ $exam->id }};
        const DURATION_MINUTES = {{ $exam->duration }};
        const DURATION_SECONDS = DURATION_MINUTES * 60;

        let originalQuestions = @json($questionsData);
        let questions = [];
        let currentIndex = 0;
        let answers = {};
        let bookmarked = {};

        // Timer variables
        let startTime = null;
        let endTime = null;

        // ==================== FULL SCREEN ====================
        function enterFullscreen() {
            const docEl = document.documentElement;
            if (docEl.requestFullscreen) docEl.requestFullscreen();
            else if (docEl.webkitRequestFullscreen) docEl.webkitRequestFullscreen();
            else if (docEl.msRequestFullscreen) docEl.msRequestFullscreen();
        }

        function enforceFullscreen() {
            if (!examActive || isSubmitting || isFinished) return;
            const isFullscreen = document.fullscreenElement || document.webkitFullscreenElement;
            if (!isFullscreen) {
                setTimeout(() => enterFullscreen(), 100);
            }
        }

        document.addEventListener('fullscreenchange', enforceFullscreen);
        document.addEventListener('webkitfullscreenchange', enforceFullscreen);

        function showToast(msg) {
            const toast = document.getElementById('toastWarning');
            toast.textContent = msg || '⚠️ Dilarang keluar dari ujian!';
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2500);
        }

        // ==================== TIMER - VERSI SEDERHANA DAN PASTI JALAN ====================
        function initTimer() {
            // Ambil waktu mulai dari localStorage
            const savedStartTime = localStorage.getItem(`exam_${EXAM_ID}_start_time`);

            if (savedStartTime && !isFinished) {
                startTime = new Date(savedStartTime);
                console.log('Timer: Menggunakan waktu tersimpan', startTime.toLocaleTimeString());
            } else {
                startTime = new Date();
                localStorage.setItem(`exam_${EXAM_ID}_start_time`, startTime.toISOString());
                console.log('Timer: Membuat waktu baru', startTime.toLocaleTimeString());
            }

            // Hitung waktu berakhir
            endTime = new Date(startTime.getTime() + (DURATION_SECONDS * 1000));
            console.log('Timer: Waktu berakhir', endTime.toLocaleTimeString());
            console.log('Timer: Durasi', DURATION_MINUTES, 'menit');
        }

        function updateTimerDisplay() {
            if (isSubmitting || isFinished) {
                console.log('Timer: Update skipped - ujian selesai');
                return;
            }

            if (!endTime) {
                console.log('Timer: endTime null, reinit...');
                initTimer();
            }

            const now = new Date();
            let remaining = Math.floor((endTime - now) / 1000);

            if (remaining < 0) remaining = 0;

            const hours = Math.floor(remaining / 3600);
            const minutes = Math.floor((remaining % 3600) / 60);
            const seconds = remaining % 60;

            const timerDisplay = document.getElementById('timerDisplay');
            if (timerDisplay) {
                timerDisplay.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }

            // Ubah warna timer
            const timerBox = document.getElementById('timerBox');
            if (timerBox) {
                if (remaining <= 300 && remaining > 0) {
                    timerBox.style.background = '#dc2626';
                    timerBox.style.animation = 'pulse 1s infinite';
                } else if (remaining <= 600) {
                    timerBox.style.background = '#ea580c';
                    timerBox.style.animation = 'none';
                } else {
                    timerBox.style.background = '#ef4444';
                    timerBox.style.animation = 'none';
                }
            }

            // Log setiap 30 detik
            if (remaining % 30 === 0) {
                console.log(`Timer: ${hours}h ${minutes}m ${seconds}s tersisa`);
            }

            // Jika waktu habis
            if (remaining <= 0 && !isSubmitting && !isFinished) {
                console.log('Timer: WAKTU HABIS! Mengirim ujian...');
                if (timerInterval) clearInterval(timerInterval);
                alert('⏰ WAKTU HABIS! Ujian akan diselesaikan.');
                document.getElementById('submitForm').submit();
            }
        }

        function startTimer() {
            // Bersihkan interval lama jika ada
            if (timerInterval) {
                clearInterval(timerInterval);
                console.log('Timer: Membersihkan interval lama');
            }

            initTimer();
            updateTimerDisplay();
            timerInterval = setInterval(updateTimerDisplay, 1000);
            console.log('Timer: Interval dimulai dengan ID', timerInterval);
        }

        // ==================== FUNGSI UJIAN ====================
        function shuffleArray(arr) {
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
            return arr;
        }

        function randomizeQuestions() {
            let shuffled = shuffleArray([...originalQuestions]);
            shuffled = shuffled.map(q => {
                if (q.type === 'pilihan_ganda' && q.options) {
                    let opts = [];
                    if (Array.isArray(q.options)) opts = [...q.options];
                    else if (typeof q.options === 'object') opts = Object.values(q.options);
                    const shuffledOpts = shuffleArray([...opts]);
                    const newOpts = {};
                    const keys = ['A', 'B', 'C', 'D'];
                    shuffledOpts.slice(0, 4).forEach((opt, idx) => {
                        if (keys[idx]) newOpts[keys[idx]] = opt;
                    });
                    return { ...q, options: newOpts };
                }
                return q;
            });
            return shuffled;
        }

        function resetExam() {
            const keys = [
                `exam_${EXAM_ID}_answers`, `exam_${EXAM_ID}_bookmarks`, `exam_${EXAM_ID}_finished`,
                `exam_${EXAM_ID}_force_exit`, `exam_${EXAM_ID}_exit_count`, `exam_${EXAM_ID}_question_order`,
                `exam_${EXAM_ID}_start_time`
            ];
            keys.forEach(k => localStorage.removeItem(k));
            answers = {};
            bookmarked = {};
            questions = randomizeQuestions();
            localStorage.setItem(`exam_${EXAM_ID}_question_order`, JSON.stringify(questions.map(q => q.id)));
            setTimeout(() => window.location.reload(), 500);
        }

        function saveAnswer(qId, answer) {
            const all = JSON.parse(localStorage.getItem(`exam_${EXAM_ID}_answers`) || '{}');
            all[qId] = answer;
            localStorage.setItem(`exam_${EXAM_ID}_answers`, JSON.stringify(all));

            fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/save-answer`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ question_id: qId, answer: answer })
            }).catch(() => {});
        }

        // ==================== RENDER SOAL ====================
        function renderQuestion(index) {
            if (index < 0 || index >= questions.length) return;

            const q = questions[index];
            document.getElementById('currentNumber').textContent = index + 1;
            document.getElementById('questionText').innerHTML = q.text || 'Soal tidak tersedia';

            const optionsArea = document.getElementById('optionsArea');
            optionsArea.innerHTML = '';

            if (q.type === 'pilihan_ganda') {
                let opts = [];
                if (q.options && typeof q.options === 'object') {
                    const keys = ['A', 'B', 'C', 'D'];
                    keys.forEach(key => {
                        if (q.options[key] && q.options[key] !== 'null' && q.options[key] !== 'undefined') {
                            opts.push({ key, value: String(q.options[key]).trim() });
                        }
                    });
                }

                if (opts.length === 0) {
                    opts = [
                        { key: 'A', value: 'Pilihan A' },
                        { key: 'B', value: 'Pilihan B' },
                        { key: 'C', value: 'Pilihan C' },
                        { key: 'D', value: 'Pilihan D' }
                    ];
                }

                opts.forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'option';

                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = `q_${q.id}`;
                    radio.value = opt.key;
                    radio.checked = answers[q.id] === opt.key;
                    radio.addEventListener('change', () => {
                        answers[q.id] = opt.key;
                        saveAnswer(q.id, opt.key);
                        updateNavBox(index);
                        updateStats();
                    });

                    const span = document.createElement('span');
                    span.className = 'option-text';
                    span.textContent = `${opt.key}. ${opt.value}`;

                    div.appendChild(radio);
                    div.appendChild(span);
                    optionsArea.appendChild(div);
                });
            } else {
                const textarea = document.createElement('textarea');
                textarea.className = 'essay-input';
                textarea.placeholder = 'Tulis jawaban essay Anda di sini...';
                textarea.value = answers[q.id] || '';
                textarea.addEventListener('input', () => {
                    answers[q.id] = textarea.value;
                    saveAnswer(q.id, textarea.value);
                    updateNavBox(index);
                    updateStats();
                });
                optionsArea.appendChild(textarea);
            }

            updateBookmarkBtn();
            updateNavActive(index);
            updateNavButtons();
        }

        function updateBookmarkBtn() {
            const q = questions[currentIndex];
            const btn = document.getElementById('bookmarkBtn');
            if (bookmarked[q.id]) {
                btn.classList.add('active');
                btn.querySelector('svg').setAttribute('fill', '#f59e0b');
            } else {
                btn.classList.remove('active');
                btn.querySelector('svg').setAttribute('fill', 'none');
            }
        }

        function toggleBookmark() {
            const q = questions[currentIndex];
            if (bookmarked[q.id]) delete bookmarked[q.id];
            else bookmarked[q.id] = true;

            localStorage.setItem(`exam_${EXAM_ID}_bookmarks`, JSON.stringify(bookmarked));
            updateBookmarkBtn();
            updateNavBox(currentIndex);
            updateStats();
        }

        function buildNavGrid() {
            const grid = document.getElementById('navGrid');
            grid.innerHTML = '';
            for (let i = 0; i < questions.length; i++) {
                const btn = document.createElement('button');
                btn.textContent = i + 1;
                btn.className = 'nav-btn';
                btn.addEventListener('click', () => {
                    currentIndex = i;
                    renderQuestion(currentIndex);
                });
                grid.appendChild(btn);
                updateNavBox(i);
            }
        }

        function updateNavBox(i) {
            const grid = document.getElementById('navGrid');
            if (i >= grid.children.length) return;

            const btn = grid.children[i];
            const q = questions[i];
            const answered = answers[q.id] && answers[q.id].trim() !== '';
            const marked = bookmarked[q.id];

            if (answered && marked) {
                btn.style.background = '#22c55e';
                btn.style.color = 'white';
                btn.style.borderColor = '#16a34a';
            } else if (answered) {
                btn.style.background = '#22c55e';
                btn.style.color = 'white';
                btn.style.borderColor = '#16a34a';
            } else if (marked) {
                btn.style.background = '#fef3c7';
                btn.style.color = '#d97706';
                btn.style.borderColor = '#f59e0b';
            } else {
                btn.style.background = 'white';
                btn.style.color = '#374151';
                btn.style.borderColor = '#e5e7eb';
            }
        }

        function updateNavActive(i) {
            const grid = document.getElementById('navGrid');
            for (let idx = 0; idx < grid.children.length; idx++) {
                if (idx === i) {
                    grid.children[idx].style.background = '#4f46e5';
                    grid.children[idx].style.color = 'white';
                    grid.children[idx].style.borderColor = '#4f46e5';
                } else {
                    updateNavBox(idx);
                }
            }
        }

        function updateNavButtons() {
            const prev = document.getElementById('prevBtn');
            const next = document.getElementById('nextBtn');
            prev.disabled = currentIndex === 0;
            next.disabled = currentIndex >= questions.length - 1;
        }

        function updateStats() {
            const answered = Object.keys(answers).filter(id => answers[id] && answers[id].trim() !== '').length;
            const bookmarkedCount = Object.keys(bookmarked).filter(id => bookmarked[id]).length;
            document.getElementById('answeredCount').textContent = answered;
            document.getElementById('unansweredCount').textContent = questions.length - answered;
            document.getElementById('bookmarkedCount').textContent = bookmarkedCount;
        }

        // ==================== INISIALISASI ====================
        function initExam() {
            console.log('InitExam: Memulai inisialisasi ujian...');

            const forceExit = localStorage.getItem(`exam_${EXAM_ID}_force_exit`);
            const savedOrder = localStorage.getItem(`exam_${EXAM_ID}_question_order`);

            if (forceExit === 'true' && !isFinished) {
                console.log('InitExam: Force exit detected, resetting...');
                resetExam();
                return;
            }

            // Load atau acak soal
            if (savedOrder && !forceExit) {
                try {
                    const order = JSON.parse(savedOrder);
                    questions = [...originalQuestions].sort((a, b) => order.indexOf(a.id) - order.indexOf(b.id));
                    console.log('InitExam: Menggunakan urutan soal tersimpan');
                } catch(e) {
                    questions = randomizeQuestions();
                    console.log('InitExam: Gagal load order, randomize baru');
                }
            } else {
                questions = randomizeQuestions();
                localStorage.setItem(`exam_${EXAM_ID}_question_order`, JSON.stringify(questions.map(q => q.id)));
                console.log('InitExam: Membuat urutan soal baru (random)');
            }

            localStorage.removeItem(`exam_${EXAM_ID}_force_exit`);

            // Load jawaban
            const savedAnswers = localStorage.getItem(`exam_${EXAM_ID}_answers`);
            if (savedAnswers) {
                try {
                    Object.assign(answers, JSON.parse(savedAnswers));
                    console.log('InitExam: Memuat jawaban tersimpan');
                } catch(e) {}
            }

            // Load bookmark
            const savedBookmarks = localStorage.getItem(`exam_${EXAM_ID}_bookmarks`);
            if (savedBookmarks) {
                try {
                    const bm = JSON.parse(savedBookmarks);
                    Object.keys(bm).forEach(id => { if (bm[id]) bookmarked[id] = true; });
                    console.log('InitExam: Memuat bookmark tersimpan');
                } catch(e) {}
            }

            // Update UI
            document.getElementById('totalQuestions').textContent = questions.length;
            document.getElementById('unansweredCount').textContent = questions.length;
            buildNavGrid();
            renderQuestion(0);
            updateStats();

            // MULAI TIMER - INI YANG PALING PENTING!
            console.log('InitExam: Memulai timer...');
            startTimer();

            console.log('InitExam: Ujian siap, total soal:', questions.length);
        }

        // ==================== MODAL ====================
        function openModal() {
            const answered = Object.keys(answers).filter(id => answers[id] && answers[id].trim() !== '').length;
            const msg = `Soal terjawab: ${answered}/${questions.length}<br>${questions.length - answered > 0 ? '⚠️ Masih ada ' + (questions.length - answered) + ' soal belum dijawab' : '✅ Semua soal sudah dijawab'}`;
            document.getElementById('modalMessage').innerHTML = msg;
            document.getElementById('finishModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('finishModal').style.display = 'none';
        }

        function submitExam() {
            closeModal();
            isSubmitting = true;
            isFinished = true;
            if (timerInterval) clearInterval(timerInterval);
            if (fullScreenInterval) clearInterval(fullScreenInterval);
            localStorage.setItem(`exam_${EXAM_ID}_finished`, 'true');
            document.getElementById('submitForm').submit();
        }

        // ==================== TOMBOL MULAI ====================
        document.getElementById('startBtn').addEventListener('click', function() {
            console.log('Start: Tombol MULAI ditekan');
            enterFullscreen();
            document.getElementById('startScreen').style.display = 'none';
            document.getElementById('examWrapper').style.display = 'block';
            examActive = true;

            if (fullScreenInterval) clearInterval(fullScreenInterval);
            fullScreenInterval = setInterval(enforceFullscreen, 2000);

            initExam();
        });

        // ==================== EVENT LISTENERS ====================
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
        document.getElementById('finishBtn').addEventListener('click', openModal);
        document.getElementById('modalCancelBtn').addEventListener('click', closeModal);
        document.getElementById('modalConfirmBtn').addEventListener('click', submitExam);

        // ==================== KEAMANAN ====================
        (function blockBackButton() {
            for (let i = 0; i < 50; i++) history.pushState(null, null, location.href);
            window.addEventListener('popstate', function(e) {
                if (examActive && !isSubmitting && !isFinished) {
                    e.preventDefault();
                    showToast('🚫 Tombol kembali dinonaktifkan!');
                    for (let i = 0; i < 50; i++) history.pushState(null, null, location.href);
                }
            });
        })();

        let touchStartX = 0;
        document.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; });
        document.addEventListener('touchend', e => {
            if (examActive && !isSubmitting && !isFinished && touchStartX < 50 && e.changedTouches[0].clientX > 100) {
                e.preventDefault();
                showToast('🚫 Gesture kembali dinonaktifkan!');
            }
        });

        window.addEventListener('beforeunload', e => {
            if (examActive && !isSubmitting && !isFinished) {
                e.preventDefault();
                e.returnValue = '⚠️ UJIAN SEDANG BERLANGSUNG!';
            }
        });

        document.addEventListener('contextmenu', e => {
            if (examActive && !isSubmitting && !isFinished) {
                e.preventDefault();
                return false;
            }
        });

        document.addEventListener('keydown', e => {
            if (!examActive || isSubmitting || isFinished) return;

            const dangerous = ['F5', 'F12', 'Escape', 'F11'];
            if (dangerous.includes(e.key)) {
                e.preventDefault();
                showToast('🚫 Shortcut dinonaktifkan!');
                return;
            }

            if ((e.ctrlKey && e.key === 'r') || (e.ctrlKey && e.key === 'R') ||
                (e.ctrlKey && e.key === 'w') || (e.ctrlKey && e.key === 'W') ||
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.shiftKey && e.key === 'C') ||
                (e.ctrlKey && e.shiftKey && e.key === 'J')) {
                e.preventDefault();
                showToast('🚫 Shortcut dinonaktifkan!');
                return;
            }

            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                currentIndex--;
                renderQuestion(currentIndex);
            } else if (e.key === 'ArrowRight' && currentIndex < questions.length - 1) {
                currentIndex++;
                renderQuestion(currentIndex);
            }
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden && examActive && !isSubmitting && !isFinished) {
                localStorage.setItem(`exam_${EXAM_ID}_force_exit`, 'true');
                showToast('⚠️ Jangan tinggalkan aplikasi ujian!');
            }
        });

        if (window.Android) {
            window.Android.onBackPressed = () => {
                if (examActive && !isSubmitting && !isFinished) {
                    showToast('🚫 Tombol back dinonaktifkan!');
                    return true;
                }
                return false;
            };
        }

        console.log('✅ Script loaded - Mode Ujian Super Aman siap');
        console.log('✅ Durasi ujian:', DURATION_MINUTES, 'menit');
    })();
</script>
</body>
</html>
