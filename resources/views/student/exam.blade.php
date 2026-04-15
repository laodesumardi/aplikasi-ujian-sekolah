<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover, shrink-to-fit=no">
    <title>Ujian: {{ $exam->title }} - Mode Aman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* RESET TOTAL - PAKSA FULL SCREEN */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html {
            width: 100%;
            height: 100%;
            background: #000;
        }

        body {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        /* START SCREEN - LAYAR PEMBUKA */
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
            backdrop-filter: blur(0px);
        }

        .start-screen h1 {
            font-size: clamp(24px, 6vw, 42px);
            margin-bottom: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .start-screen p {
            font-size: clamp(14px, 4vw, 18px);
            margin-bottom: 40px;
            opacity: 0.95;
            padding: 0 30px;
            max-width: 500px;
            line-height: 1.6;
        }

        .start-screen .warning-text {
            font-size: 12px;
            margin-top: 30px;
            opacity: 0.7;
            position: absolute;
            bottom: 30px;
            left: 0;
            right: 0;
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .btn-start:active {
            transform: scale(0.96);
        }

        /* KONTEN UJIAN - AWALNYA TERSEMBUNYI */
        .exam-wrapper {
            display: none;
            width: 100%;
            height: 100%;
            background: #f0f2f5;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* HEADER UJIAN */
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

        .exam-title h2 {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .exam-title p {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .timer-box svg {
            width: 20px;
            height: 20px;
        }

        .timer-display {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* KONTEN UTAMA */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* DESKTOP: 2 KOLOM */
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

        /* CARD SOAL */
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
            line-height: 1.5;
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
            transition: all 0.2s;
        }

        .btn-bookmark.active {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        /* OPSI JAWABAN */
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
            accent-color: #4f46e5;
        }

        .option-text {
            flex: 1;
            font-size: 15px;
            color: #374151;
            line-height: 1.4;
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

        /* TOMBOL NAVIGASI SOAL */
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
            transition: all 0.2s;
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

        /* SIDEBAR NAVIGASI */
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
            transition: all 0.15s;
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
            transition: all 0.2s;
        }

        .btn-finish:active {
            transform: scale(0.98);
        }

        /* MODAL */
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
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: white;
            border-radius: 28px;
            padding: 28px;
            width: 90%;
            max-width: 360px;
            text-align: center;
        }

        .modal-content h3 {
            font-size: 22px;
            margin-bottom: 16px;
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

        /* SCROLLBAR */
        .exam-wrapper::-webkit-scrollbar {
            width: 6px;
        }

        .exam-wrapper::-webkit-scrollbar-track {
            background: #e5e7eb;
        }

        .exam-wrapper::-webkit-scrollbar-thumb {
            background: #4f46e5;
            border-radius: 10px;
        }

        /* Peringatan floating */
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .toast-warning.show {
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>
<body>

<!-- LAYAR PEMBUKA (WAJIB UNTUK TRIGGER FULL SCREEN) -->
<div class="start-screen" id="startScreen">
    <h1>📱🔒 {{ $exam->title }}</h1>
    <p>Mode Ujian Super Aman<br>Layar Penuh Total</p>
    <button class="btn-start" id="startBtn">🚀 MULAI UJIAN</button>
    <div class="warning-text">
        ⚠️ Dilarang keluar dari aplikasi selama ujian berlangsung<br>
        Sistem akan mendeteksi setiap upaya keluar
    </div>
</div>

<!-- PEMBUNGKUS UJIAN -->
<div class="exam-wrapper" id="examWrapper">

    <!-- HEADER -->
    <div class="exam-header">
        <div class="header-inner">
            <div class="exam-title">
                <h2>{{ $exam->title }}</h2>
                <p>{{ $exam->subject->name ?? 'Ujian' }} | Durasi: {{ $exam->duration }} menit</p>
            </div>
            <div class="timer-box" id="timerBox">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    <path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zm0 2.25a7.5 7.5 0 110 15 7.5 7.5 0 010-15z" clip-rule="evenodd" />
                </svg>
                <span class="timer-display" id="timerDisplay">00:00:00</span>
            </div>
        </div>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="main-content">

        <!-- BAGIAN SOAL -->
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

        <!-- BAGIAN NAVIGASI -->
        <div class="nav-section">
            <div class="nav-card">
                <h3 style="margin-bottom: 16px; font-size: 16px;">📋 Navigasi Soal</h3>

                <div class="stats">
                    <div class="stat-item">
                        <span class="stat-label">📊 Total Soal</span>
                        <span class="stat-value" id="totalQuestions">{{ $questions->count() }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">✅ Sudah Dikerjakan</span>
                        <span class="stat-value answered" id="answeredCount">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">⭐ Ditandai</span>
                        <span class="stat-value bookmarked" id="bookmarkedCount">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">⏳ Belum Dikerjakan</span>
                        <span class="stat-value unanswered" id="unansweredCount">{{ $questions->count() }}</span>
                    </div>
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

<!-- MODAL KONFIRMASI SELESAI -->
<div class="modal" id="finishModal">
    <div class="modal-content">
        <h3>✅ Selesaikan Ujian?</h3>
        <p id="modalMessage" style="color: #6b7280; margin-bottom: 20px;"></p>
        <div class="modal-buttons">
            <button style="background: #e5e7eb;" onclick="closeModal()">Batal</button>
            <button style="background: #ef4444; color: white;" onclick="submitExam()">Ya, Selesai</button>
        </div>
    </div>
</div>

<!-- TOAST PERINGATAN -->
<div class="toast-warning" id="toastWarning">⚠️ Dilarang keluar dari ujian!</div>

<script>
    // ==================== FULL SCREEN TOTAL (DESKTOP + HP) ====================

    let examActive = false;
    let isSubmitting = false;
    let isFinished = localStorage.getItem(`exam_{{ $exam->id }}_finished`) === 'true';
    let fullScreenInterval = null;

    // Fungsi request full screen (MULTI METHOD)
    function enterFullscreen() {
        const docEl = document.documentElement;

        // Chrome, Firefox, Edge, Safari, Android WebView
        if (docEl.requestFullscreen) {
            docEl.requestFullscreen();
        } else if (docEl.webkitRequestFullscreen) { // Safari, Chrome Android lama
            docEl.webkitRequestFullscreen();
        } else if (docEl.msRequestFullscreen) { // IE/Edge
            docEl.msRequestFullscreen();
        } else if (docEl.mozRequestFullScreen) { // Firefox
            docEl.mozRequestFullScreen();
        }

        // Untuk Kodular WebView (custom)
        if (window.Android && window.Android.enterFullscreen) {
            window.Android.enterFullscreen();
        }

        console.log('Fullscreen requested');
    }

    // Fungsi keluar full screen (TIDAK AKAN DIGUNAKAN SELAMA UJIAN)
    function exitFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }

    // Cek status full screen - jika keluar, paksa masuk lagi
    function enforceFullscreen() {
        if (!examActive || isSubmitting || isFinished) return;

        const isFullscreen = document.fullscreenElement ||
                            document.webkitFullscreenElement ||
                            document.msFullscreenElement;

        if (!isFullscreen) {
            console.log('Fullscreen lost! Re-entering...');
            showToast('🔒 Mode layar penuh diaktifkan kembali!');
            setTimeout(() => enterFullscreen(), 100);
        }
    }

    // Event listener perubahan full screen
    document.addEventListener('fullscreenchange', enforceFullscreen);
    document.addEventListener('webkitfullscreenchange', enforceFullscreen);
    document.addEventListener('mozfullscreenchange', enforceFullscreen);

    // Toast notifikasi
    function showToast(message) {
        const toast = document.getElementById('toastWarning');
        toast.textContent = message || '⚠️ Dilarang keluar dari ujian!';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2500);
    }

    // ==================== TOMBOL MULAI ====================
    document.getElementById('startBtn').addEventListener('click', function() {
        // 1. Masuk full screen
        enterFullscreen();

        // 2. Sembunyikan start screen
        document.getElementById('startScreen').style.display = 'none';

        // 3. Tampilkan ujian
        document.getElementById('examWrapper').style.display = 'block';

        // 4. Aktifkan mode ujian
        examActive = true;

        // 5. Mulai interval pengecekan full screen (setiap 2 detik)
        if (fullScreenInterval) clearInterval(fullScreenInterval);
        fullScreenInterval = setInterval(enforceFullscreen, 2000);

        // 6. Inisialisasi ujian
        initExam();

        // 7. Catat waktu mulai
        if (!localStorage.getItem(`exam_{{ $exam->id }}_start_time`)) {
            localStorage.setItem(`exam_{{ $exam->id }}_start_time`, new Date().toISOString());
        }

        console.log('✅ Ujian dimulai - Fullscreen mode aktif');
    });

    // ==================== VARIABEL UJIAN ====================
    const EXAM_ID = {{ $exam->id }};
    let originalQuestions = @json($questionsData);
    let questions = [];
    let currentIndex = 0;
    let answers = {};
    let bookmarked = {};
    let timerInterval = null;

    // Fungsi acak array (Fisher-Yates)
    function shuffle(arr) {
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
        return arr;
    }

    // Acak soal dan opsi
    function randomizeQuestions() {
        let shuffled = shuffle([...originalQuestions]);

        shuffled = shuffled.map(q => {
            if (q.type === 'pilihan_ganda' && q.options) {
                let opts = [];
                if (Array.isArray(q.options)) opts = [...q.options];
                else if (typeof q.options === 'object') opts = Object.values(q.options);

                const shuffledOpts = shuffle([...opts]);
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

    // Inisialisasi ujian
    function initExam() {
        const forceExit = localStorage.getItem(`exam_${EXAM_ID}_force_exit`);
        const savedOrder = localStorage.getItem(`exam_${EXAM_ID}_question_order`);

        // Cek perlu reset
        if (forceExit === 'true' && !isFinished) {
            resetExam();
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
            localStorage.setItem(`exam_${EXAM_ID}_question_order`, JSON.stringify(questions.map(q => q.id)));
        }

        // Bersihkan flag force exit
        localStorage.removeItem(`exam_${EXAM_ID}_force_exit`);

        // Load jawaban tersimpan
        const savedAnswers = localStorage.getItem(`exam_${EXAM_ID}_answers`);
        if (savedAnswers) {
            try {
                Object.assign(answers, JSON.parse(savedAnswers));
            } catch(e) {}
        }

        // Load bookmark
        const savedBookmarks = localStorage.getItem(`exam_${EXAM_ID}_bookmarks`);
        if (savedBookmarks) {
            try {
                const bm = JSON.parse(savedBookmarks);
                Object.keys(bm).forEach(id => { if (bm[id]) bookmarked[id] = true; });
            } catch(e) {}
        }

        // Update UI
        document.getElementById('totalQuestions').textContent = questions.length;
        buildNavGrid();
        renderQuestion(0);
        updateStats();
        startTimer();
    }

    // Reset ujian
    function resetExam() {
        // Hapus semua data
        const keys = [`exam_${EXAM_ID}_answers`, `exam_${EXAM_ID}_bookmarks`, `exam_${EXAM_ID}_finished`,
                      `exam_${EXAM_ID}_force_exit`, `exam_${EXAM_ID}_exit_count`, `exam_${EXAM_ID}_question_order`,
                      `exam_${EXAM_ID}_start_time`];
        keys.forEach(k => localStorage.removeItem(k));

        // Reset variabel
        answers = {};
        bookmarked = {};

        // Acak ulang soal
        questions = randomizeQuestions();
        localStorage.setItem(`exam_${EXAM_ID}_question_order`, JSON.stringify(questions.map(q => q.id)));

        // Reload halaman setelah reset
        setTimeout(() => window.location.reload(), 500);
    }

    // Render soal
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

    function updateBookmarkBtn() {
        const q = questions[currentIndex];
        const isBookmarked = bookmarked[q.id];
        const btn = document.getElementById('bookmarkBtn');

        if (isBookmarked) {
            btn.classList.add('active');
            btn.querySelector('svg').setAttribute('fill', '#f59e0b');
            btn.querySelector('svg').setAttribute('stroke', '#f59e0b');
        } else {
            btn.classList.remove('active');
            btn.querySelector('svg').setAttribute('fill', 'none');
            btn.querySelector('svg').setAttribute('stroke', 'currentColor');
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
            const btn = grid.children[idx];
            if (idx === i) {
                btn.style.background = '#4f46e5';
                btn.style.color = 'white';
                btn.style.borderColor = '#4f46e5';
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

    // TIMER
    function startTimer() {
        const durationMinutes = {{ $exam->duration }};
        let startTime = localStorage.getItem(`exam_${EXAM_ID}_start_time`);
        if (!startTime) {
            startTime = new Date().toISOString();
            localStorage.setItem(`exam_${EXAM_ID}_start_time`, startTime);
        }

        const endTime = new Date(new Date(startTime).getTime() + (durationMinutes * 60 * 1000));

        function updateTimer() {
            if (isSubmitting || isFinished) return;
            const remaining = Math.max(0, Math.floor((endTime - new Date()) / 1000));
            const h = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const m = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            document.getElementById('timerDisplay').textContent = `${h}:${m}:${s}`;

            if (remaining <= 0) {
                clearInterval(timerInterval);
                alert('⏰ Waktu habis! Ujian akan diselesaikan.');
                document.getElementById('submitForm').submit();
            }
        }

        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    }

    // MODAL
    function openModal() {
        const answered = Object.keys(answers).filter(id => answers[id] && answers[id].trim() !== '').length;
        document.getElementById('modalMessage').innerHTML = `Soal terjawab: ${answered}/${questions.length}<br>${questions.length - answered > 0 ? '⚠️ Masih ada ' + (questions.length - answered) + ' soal belum dijawab' : '✅ Semua soal sudah dijawab'}`;
        document.getElementById('finishModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('finishModal').style.display = 'none';
    }

    function submitExam() {
        closeModal();
        isSubmitting = true;
        isFinished = true;
        localStorage.setItem(`exam_${EXAM_ID}_finished`, 'true');
        if (fullScreenInterval) clearInterval(fullScreenInterval);
        document.getElementById('submitForm').submit();
    }

    // ==================== KEAMANAN ====================

    // Cegah tombol back (History API)
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

    // Cegah gesture back (swipe dari kiri)
    let touchStartX = 0;
    document.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; });
    document.addEventListener('touchend', e => {
        if (examActive && !isSubmitting && !isFinished && touchStartX < 50 && e.changedTouches[0].clientX > 100) {
            e.preventDefault();
            showToast('🚫 Gesture kembali dinonaktifkan!');
        }
    });

    // Cegah refresh/tutup
    window.addEventListener('beforeunload', e => {
        if (examActive && !isSubmitting && !isFinished) {
            e.preventDefault();
            e.returnValue = '⚠️ UJIAN SEDANG BERLANGSUNG! Jangan keluar!';
        }
    });

    // Cegah menu konteks (klik kanan)
    document.addEventListener('contextmenu', e => {
        if (examActive && !isSubmitting && !isFinished) {
            e.preventDefault();
            return false;
        }
    });

    // Cegah shortcut keyboard berbahaya
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

        // Navigasi panah kiri/kanan tetap diizinkan
        if (e.key === 'ArrowLeft' && currentIndex > 0) {
            currentIndex--;
            renderQuestion(currentIndex);
        } else if (e.key === 'ArrowRight' && currentIndex < questions.length - 1) {
            currentIndex++;
            renderQuestion(currentIndex);
        }
    });

    // Deteksi minimize app
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && examActive && !isSubmitting && !isFinished) {
            localStorage.setItem(`exam_${EXAM_ID}_force_exit`, 'true');
            showToast('⚠️ Jangan tinggalkan aplikasi ujian!');
        }
    });

    // Override untuk Kodular
    if (window.Android) {
        window.Android.onBackPressed = () => {
            if (examActive && !isSubmitting && !isFinished) {
                showToast('🚫 Tombol back dinonaktifkan!');
                return true;
            }
            return false;
        };
    }

    // Event listeners UI
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

    console.log('✅ Mode Ujian Super Aman siap - Fullscreen Desktop + HP');
</script>
</body>
</html>
