<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Ujian: {{ $exam->title }}</title>
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
            padding: 8px;
            font-size: 12px;
            z-index: 10000;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
        }

        #securityWarning.show {
            transform: translateY(0);
        }

        #blockOverlay {
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

        #fullscreenIndicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 11px;
            z-index: 10001;
            pointer-events: none;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
        <p>Memeriksa status ujian...</p>
    </div>

    <div id="securityWarning">⚠️ DILARANG KELUAR DARI APLIKASI UJIAN!</div>
    <div id="blockOverlay">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h2 style="margin: 20px 0 10px;">🚫 AKSES DITOLAK!</h2>
        <p>Anda tidak diizinkan keluar dari aplikasi ujian.<br>Silakan lanjutkan mengerjakan soal.</p>
        <button onclick="hideBlockOverlay()" style="margin-top: 20px; padding: 10px 30px; background: #4f46e5; border: none; border-radius: 8px; color: white; font-weight: bold;">Kembali ke Ujian</button>
    </div>

    <div class="exam-container" id="examContainer" style="display: none;">
        <header class="sticky top-0 z-50 bg-white shadow-md">
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
                                <button id="bookmarkBtn" type="button" class="inline-flex items-center justify-center w-10 h-10 transition-colors border-2 border-gray-300 rounded-lg hover:border-yellow-400 hover:bg-yellow-50">
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
                                <span class="font-semibold text-gray-900" id="totalQuestions">{{ $questions->count() }}</span>
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
                                <span class="font-semibold text-gray-700" id="unansweredCount">{{ $questions->count() }}</span>
                            </div>
                        </div>
                        <div id="navGrid" class="grid grid-cols-5 gap-2 mb-6 overflow-y-auto max-h-96"></div>
                        <div class="mt-auto">
                            <form id="submitForm" method="POST" action="{{ route('siswa.exam.submit', $exam->id) }}">
                                @csrf
                                <button type="button" id="finishBtn" class="w-full px-4 py-3 font-semibold text-white transition-colors bg-red-600 rounded-lg shadow-md hover:bg-red-700">Selesai Ujian</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="fullscreenIndicator">🔒 Mode Ujian Penuh</div>

    <script>
        // ============ PENGECEKAN AWAL: APAKAH UJIAN SUDAH SELESAI? ============
        (function checkExamStatus() {
            // Cek dari localStorage
            const finished = localStorage.getItem(`exam_{{ $exam->id }}_finished`);
            const completedAt = localStorage.getItem(`exam_{{ $exam->id }}_completed_at`);

            // Cek dari server side (Laravel)
            @if(isset($examResult) && $examResult && $examResult->completed_at)
                // Jika sudah selesai dari server, redirect ke result
                window.location.href = "{{ route('siswa.exam.result', $exam->id) }}";
                return;
            @endif

            // Jika localStorage menandai sudah selesai
            if (finished === 'true') {
                window.location.href = "{{ url('/siswa/ujian') }}/{{ $exam->id }}/result";
                return;
            }

            // Cek apakah waktu ujian sudah habis
            const startedAtServer = new Date('{{ isset($examResult) && $examResult->started_at ? $examResult->started_at : now() }}');
            const durationMinutes = {{ $exam->duration }};
            const endTime = new Date(startedAtServer.getTime() + (durationMinutes * 60 * 1000));
            const now = new Date();

            if (now > endTime) {
                // Waktu habis, langsung submit otomatis
                alert("Waktu ujian telah habis! Anda akan diarahkan ke halaman hasil.");
                window.location.href = "{{ url('/siswa/ujian') }}/{{ $exam->id }}/result";
                return;
            }

            // Jika semua aman, tampilkan konten ujian
            setTimeout(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
                document.getElementById('examContainer').style.display = 'block';
            }, 500);
        })();

        // ============ DEKLARASI VARIABEL GLOBAL ============
        let isSubmittingExam = false;
        let hasFinishedExam = localStorage.getItem(`exam_{{ $exam->id }}_finished`) === 'true';
        let keluar = parseInt(localStorage.getItem(`exam_{{ $exam->id }}_keluar`) || '0');
        let answers = {};
        let bookmarked = {};

        // Data ujian
        const examId = {{ $exam->id }};
        const durationMinutes = {{ $exam->duration }};
        const questions = @json($questionsData);
        const savedAnswers = @json($answers ?? []);
        const totalQuestions = questions.length;

        let currentIndex = 0;

        // Jika tidak ada soal, redirect
        if (totalQuestions === 0) {
            alert('Tidak ada soal untuk ujian ini.');
            window.location.href = "{{ url('/siswa/dashboard') }}";
        }

        // Load saved answers
        if (savedAnswers && typeof savedAnswers === 'object') {
            Object.keys(savedAnswers).forEach(questionId => {
                answers[questionId] = savedAnswers[questionId];
            });
        }

        // ============ WINDOW.BEFOREUNLOAD ============
        window.onbeforeunload = function(e) {
            if (!isSubmittingExam && !hasFinishedExam) {
                const answeredCount = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
                if (answeredCount < totalQuestions) {
                    const message = "⚠️ PERINGATAN! Jika keluar, ujian akan diulang dari awal!";
                    e.preventDefault();
                    e.returnValue = message;
                    return message;
                }
            }
        };

        // ============ VISIBILITY CHANGE DENGAN COUNTER ============
        document.addEventListener("visibilitychange", function() {
            if (document.hidden && !isSubmittingExam && !hasFinishedExam) {
                keluar++;
                localStorage.setItem(`exam_{{ $exam->id }}_keluar`, keluar);

                // Tampilkan alert
                alert("⚠️ PERINGATAN! Jangan keluar dari ujian! (" + keluar + "x)");

                // Tampilkan warning di layar
                showSecurityWarning("⚠️ Jangan tinggalkan ujian! (" + keluar + "x)");

                // Jika keluar 3 kali atau lebih, reset ujian
                if (keluar >= 3) {
                    showSecurityWarning("❌ UJIAN DIULANG! Anda terlalu sering keluar dari aplikasi.");
                    setTimeout(function() {
                        // Reset localStorage
                        localStorage.removeItem(`exam_{{ $exam->id }}_keluar`);
                        localStorage.removeItem(`exam_{{ $exam->id }}_finished`);
                        localStorage.removeItem(`exam_{{ $exam->id }}_bookmarks`);
                        // Redirect ke halaman reset
                        window.location.href = "{{ url('/siswa/ujian') }}/{{ $exam->id }}/reset";
                    }, 2000);
                }
            }
        });

        // ============ FULLSCREEN MODE ============
        function enableFullscreen() {
            const elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen().catch(err => console.log('Fullscreen error:', err));
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        }

        // Cegah keluar dari fullscreen
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement && !isSubmittingExam && !hasFinishedExam) {
                showSecurityWarning("🔒 Mode layar penuh wajib diaktifkan!");
                setTimeout(enableFullscreen, 100);
            }
        });

        // Aktifkan fullscreen jika belum selesai
        if (!hasFinishedExam && !isSubmittingExam) {
            setTimeout(enableFullscreen, 1000);
        }

        // Cegah tombol F11 dan ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F11' || (e.key === 'Escape' && document.fullscreenElement)) {
                e.preventDefault();
                showSecurityWarning("🚫 Tombol F11/Escape dinonaktifkan!");
                return false;
            }

            // Cegah Ctrl+R, Ctrl+F5, Ctrl+W, Ctrl+T
            if ((e.ctrlKey && (e.key === 'r' || e.key === 'R' || e.key === 'w' || e.key === 'W' || e.key === 't' || e.key === 'T')) ||
                (e.key === 'F5')) {
                e.preventDefault();
                showSecurityWarning("🚫 Refresh dan shortcut keyboard dinonaktifkan!");
                return false;
            }
        });

        // ============ FUNGSI KEAMANAN LAINNYA ============
        function showSecurityWarning(message) {
            const warning = document.getElementById('securityWarning');
            warning.textContent = message || '⚠️ DILARANG KELUAR DARI APLIKASI UJIAN!';
            warning.classList.add('show');
            setTimeout(() => {
                warning.classList.remove('show');
            }, 3000);
        }

        function showBlockOverlay() {
            document.getElementById('blockOverlay').style.display = 'flex';
            setTimeout(() => {
                hideBlockOverlay();
            }, 3000);
        }

        function hideBlockOverlay() {
            document.getElementById('blockOverlay').style.display = 'none';
        }

        // Cegah tombol back (History API)
        (function preventBackButton() {
            history.pushState(null, null, location.href);
            window.addEventListener('popstate', function(event) {
                if (!isSubmittingExam && !hasFinishedExam) {
                    showSecurityWarning('🚫 Tombol kembali dinonaktifkan!');
                    showBlockOverlay();
                    history.pushState(null, null, location.href);
                } else {
                    history.pushState(null, null, location.href);
                }
            });
        })();

        // Cegah gesture back di HP
        let touchStartXPosition = 0;
        document.addEventListener('touchstart', function(e) {
            touchStartXPosition = e.changedTouches[0].screenX;
        }, false);

        document.addEventListener('touchend', function(e) {
            const touchEndXPosition = e.changedTouches[0].screenX;
            const deltaX = touchEndXPosition - touchStartXPosition;

            if (touchStartXPosition < 40 && deltaX > 70 && !isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                showSecurityWarning('🚫 Gesture kembali dinonaktifkan!');
                showBlockOverlay();
                return false;
            }
        }, false);

        // Nonaktifkan menu konteks
        document.addEventListener('contextmenu', function(e) {
            if (!isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                return false;
            }
        });

        // Nonaktifkan copy-paste (kecuali di textarea)
        document.addEventListener('copy', function(e) {
            if (!isSubmittingExam && !hasFinishedExam && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                showSecurityWarning('❌ Menyalin teks tidak diizinkan!');
                return false;
            }
        });

        document.addEventListener('cut', function(e) {
            if (!isSubmittingExam && !hasFinishedExam && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                showSecurityWarning('❌ Memotong teks tidak diizinkan!');
                return false;
            }
        });

        document.addEventListener('paste', function(e) {
            if (!isSubmittingExam && !hasFinishedExam && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                showSecurityWarning('❌ Menempel teks hanya diizinkan pada jawaban essay!');
                return false;
            }
        });

        // Lock orientation
        if (!hasFinishedExam && !isSubmittingExam) {
            if (screen.orientation && screen.orientation.lock) {
                screen.orientation.lock('portrait').catch(e => console.log('Orientation lock error:', e));
            }
        }

        // ============ KODE UJIAN UTAMA ============
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

        // Load bookmarks
        const savedBookmarks = localStorage.getItem(`exam_${examId}_bookmarks`);
        if (savedBookmarks) {
            try {
                const bookmarks = JSON.parse(savedBookmarks);
                Object.keys(bookmarks).forEach(qId => {
                    if (bookmarks[qId]) bookmarked[qId] = true;
                });
            } catch(e) {}
        }

        function renderQuestion(index) {
            if (index < 0 || index >= totalQuestions) return;
            const q = questions[index];
            if (!q) return;

            currentNumberEl.textContent = index + 1;
            questionTextEl.innerHTML = q.text || 'Memuat soal...';
            optionsAreaEl.innerHTML = '';

            if (q.type === 'pilihan_ganda') {
                let optionsList = [];
                if (q.options) {
                    if (Array.isArray(q.options)) {
                        q.options.slice(0, 4).forEach((opt, idx) => {
                            const key = String.fromCharCode(65 + idx);
                            let value = typeof opt === 'string' ? opt : (opt.label || opt.text || String(opt));
                            optionsList.push({ key, value: value.trim() });
                        });
                    } else if (typeof q.options === 'object') {
                        const keys = ['A', 'B', 'C', 'D'];
                        keys.forEach(key => {
                            if (q.options[key]) {
                                optionsList.push({ key, value: String(q.options[key]).trim() });
                            }
                        });
                    }
                }

                const optionKeys = ['A', 'B', 'C', 'D'];
                optionKeys.forEach(key => {
                    if (!optionsList.find(o => o.key === key)) {
                        optionsList.push({ key, value: '' });
                    }
                });
                optionsList.sort((a,b) => a.key.localeCompare(b.key));

                optionsList.forEach(option => {
                    const wrapper = document.createElement('label');
                    wrapper.className = 'flex items-center gap-3 px-4 py-3 rounded-lg border-2 border-gray-200 hover:border-primary hover:bg-primary/5 cursor-pointer';

                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = `q_${q.id}`;
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
                    text.textContent = option.value ? `${option.key}. ${option.value}` : `${option.key}. (Tidak ada teks)`;

                    wrapper.appendChild(radio);
                    wrapper.appendChild(text);
                    optionsAreaEl.appendChild(wrapper);
                });
            } else if (q.type === 'essay' || q.type === 'esai') {
                const textarea = document.createElement('textarea');
                textarea.className = 'w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary';
                textarea.rows = 6;
                textarea.placeholder = 'Tulis jawaban Anda di sini...';
                textarea.value = answers[q.id] || '';
                textarea.addEventListener('input', () => {
                    answers[q.id] = textarea.value;
                    saveAnswer(q.id, textarea.value);
                    updateNavBox(index);
                    updateCounts();
                });
                optionsAreaEl.appendChild(textarea);
            } else {
                optionsAreaEl.innerHTML = '<div class="p-4 text-red-600 rounded-lg bg-red-50">Jenis soal tidak dikenal: ' + q.type + '</div>';
            }

            updateActiveBox(index);
            updatePrevNextState();
            updateBookmarkButton();
        }

        function updateBookmarkButton() {
            if (!questions[currentIndex]) return;
            const q = questions[currentIndex];
            const isBookmarked = bookmarked[q.id] || false;

            if (isBookmarked) {
                bookmarkBtn.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-yellow-400 bg-yellow-50';
                bookmarkIcon.className = 'w-5 h-5 text-yellow-600';
                bookmarkIcon.innerHTML = '<path d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" fill="currentColor" stroke="none"/>';
            } else {
                bookmarkBtn.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-gray-300 hover:border-yellow-400 hover:bg-yellow-50';
                bookmarkIcon.className = 'w-5 h-5 text-gray-400';
                bookmarkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>';
            }
        }

        function toggleBookmark() {
            const q = questions[currentIndex];
            if (bookmarked[q.id]) {
                delete bookmarked[q.id];
            } else {
                bookmarked[q.id] = true;
            }
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
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ question_id: questionId, answer: answer })
            }).catch(err => console.error('Error saving answer:', err));
        }

        function updatePrevNextState() {
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= totalQuestions - 1;

            prevBtn.style.opacity = prevBtn.disabled ? '0.5' : '1';
            nextBtn.style.opacity = nextBtn.disabled ? '0.5' : '1';
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
        }

        function updateNavBox(i) {
            if (i >= navGridEl.children.length) return;
            const btn = navGridEl.children[i];
            const q = questions[i];
            const isAnswered = answers[q.id] && answers[q.id].trim() !== '';
            const isBookmarked = bookmarked[q.id] || false;
            const isActive = i === currentIndex;

            let className = 'w-12 h-12 rounded-lg flex items-center justify-center text-sm font-semibold border-2 transition-all';
            if (isActive) className += ' bg-primary text-white border-primary shadow-md';
            else if (isAnswered) className += ' bg-green-500 text-white border-green-600';
            else if (isBookmarked) className += ' bg-yellow-100 text-yellow-900 border-yellow-400';
            else className += ' bg-white text-gray-700 border-gray-300 hover:bg-gray-50';

            btn.className = className;
        }

        function updateActiveBox(i) {
            for (let idx = 0; idx < navGridEl.children.length; idx++) updateNavBox(idx);
        }

        function updateCounts() {
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            answeredCountEl.textContent = answered;
            unansweredCountEl.textContent = totalQuestions - answered;
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
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            const unanswered = totalQuestions - answered;

            let message = `Yakin ingin menyelesaikan ujian?\n\n`;
            message += `✅ Sudah dikerjakan: ${answered} soal\n`;
            message += `⚠️ Belum dikerjakan: ${unanswered} soal\n\n`;

            if (unanswered > 0) {
                message += `❗ PERINGATAN: Masih ada ${unanswered} soal yang belum dijawab!\n\n`;
            }

            message += `Tekan OK untuk menyelesaikan ujian.`;

            if (confirm(message)) {
                isSubmittingExam = true;
                hasFinishedExam = true;
                localStorage.setItem(`exam_{{ $exam->id }}_finished`, 'true');
                localStorage.setItem(`exam_{{ $exam->id }}_completed_at`, new Date().toISOString());
                document.getElementById('submitForm').submit();
            }
        });

        // Timer
        const startedAt = new Date('{{ isset($examResult) && $examResult->started_at ? $examResult->started_at : now() }}');
        const endTime = new Date(startedAt.getTime() + (durationMinutes * 60 * 1000));
        let timerInterval = null;

        function renderTimer() {
            const now = new Date();
            const remaining = Math.max(0, Math.floor((endTime - now) / 1000));
            const h = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const m = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            timerDisplay.textContent = `${h}:${m}:${s}`;

            // Ubah warna timer jika tinggal 5 menit
            const timerBox = document.getElementById('timerBox');
            if (remaining <= 300 && remaining > 0) {
                timerBox.className = 'flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg shadow border-2 border-red-800 animate-pulse';
            } else if (remaining <= 600 && remaining > 0) {
                timerBox.className = 'flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg shadow border-2 border-orange-800';
            }

            if (remaining <= 0 && !isSubmittingExam) {
                clearInterval(timerInterval);
                alert('Waktu ujian telah habis! Jawaban akan disimpan dan ujian akan diselesaikan.');
                isSubmittingExam = true;
                hasFinishedExam = true;
                localStorage.setItem(`exam_{{ $exam->id }}_finished`, 'true');
                document.getElementById('submitForm').submit();
            }
        }

        timerInterval = setInterval(renderTimer, 1000);
        renderTimer();

        // Initialize
        if (totalQuestions > 0) {
            buildNavGrid();
            renderQuestion(0);
            updateCounts();
        }

        console.log('✅ Mode keamanan ujian aktif');
        console.log('📋 Total soal:', totalQuestions);
        console.log('⏰ Waktu berakhir:', endTime.toLocaleString());
    </script>
</body>
</html>
