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

        #resetOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 99998;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
            padding: 20px;
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
        <button onclick="hideBlockOverlayAndReload()" style="margin-top: 30px; padding: 12px 40px; background: #4f46e5; border: none; border-radius: 10px; color: white; font-weight: bold; font-size: 16px;">Kembali ke Ujian</button>
    </div>

    <div id="resetOverlay">
        <div class="spinner"></div>
        <h2 style="margin-bottom: 10px;">🔄 MERESET UJIAN...</h2>
        <p>Terjadi pelanggaran keamanan. Ujian akan dimulai dari awal.</p>
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
        // ============ VARIABEL GLOBAL ============
        let isSubmittingExam = false;
        let hasFinishedExam = localStorage.getItem(`exam_{{ $exam->id }}_finished`) === 'true';
        let blockCount = 0;
        let backPressCount = 0;
        let isResetting = false;

        const EXAM_ID = {{ $exam->id }};

        // ============ FITUR AUTO-RESET SAAT KELUAR PAKSA ============

        function showResetOverlay(message) {
            const overlay = document.getElementById('resetOverlay');
            const messageEl = overlay.querySelector('p:first-of-type');
            if (messageEl) messageEl.innerHTML = message || 'Terjadi pelanggaran keamanan. Ujian akan dimulai dari awal.';
            overlay.style.display = 'flex';
        }

        function hideResetOverlay() {
            document.getElementById('resetOverlay').style.display = 'none';
        }

        async function resetExamAndRedirect() {
            if (isResetting) return;
            isResetting = true;

            console.log('🔄 Memulai proses reset ujian...');
            showResetOverlay('🔄 MERESET UJIAN... Menghapus semua jawaban.');

            // Hapus semua data dari localStorage
            localStorage.removeItem(`exam_${EXAM_ID}_answers`);
            localStorage.removeItem(`exam_${EXAM_ID}_bookmarks`);
            localStorage.removeItem(`exam_${EXAM_ID}_finished`);
            localStorage.removeItem(`exam_${EXAM_ID}_force_exit`);
            localStorage.removeItem(`exam_${EXAM_ID}_exit_time`);
            localStorage.removeItem(`exam_${EXAM_ID}_exit_count`);
            localStorage.removeItem(`exam_${EXAM_ID}_minimize_time`);
            localStorage.removeItem(`exam_${EXAM_ID}_minimize_count`);
            localStorage.removeItem(`exam_${EXAM_ID}_temp_answers`);

            // Kirim reset ke server
            try {
                await fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/reset`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        reason: 'force_exit_detected',
                        timestamp: new Date().toISOString(),
                        exit_count: exitCount
                    })
                });
            } catch(e) {
                console.error('Error sending reset to server:', e);
            }

            // Tunggu sebentar lalu reload
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }

        // 1. DETEKSI KELUAR PAKSA (Force Close / Minimize)
        let exitCount = parseInt(localStorage.getItem(`exam_${EXAM_ID}_exit_count`) || '0');

        document.addEventListener('visibilitychange', function() {
            if (document.hidden && !isSubmittingExam && !hasFinishedExam && !isResetting) {
                exitCount++;
                localStorage.setItem(`exam_${EXAM_ID}_exit_count`, exitCount);
                localStorage.setItem(`exam_${EXAM_ID}_force_exit`, 'true');
                localStorage.setItem(`exam_${EXAM_ID}_exit_time`, new Date().toISOString());

                console.log(`⚠️ Aplikasi diminimize/ditutup (ke-${exitCount}) - Akan reset saat kembali`);

                // Kirim notifikasi ke server
                fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/force-exit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        timestamp: new Date().toISOString(),
                        exit_count: exitCount
                    })
                }).catch(() => {});
            }
        });

        // 2. CEK SAAT APLIKASI DIBUKA KEMBALI (HALAMAN LOAD/RELOAD)
        window.addEventListener('load', function() {
            const forceExit = localStorage.getItem(`exam_${EXAM_ID}_force_exit`);
            const exitTime = localStorage.getItem(`exam_${EXAM_ID}_exit_time`);
            const savedExitCount = localStorage.getItem(`exam_${EXAM_ID}_exit_count`);

            console.log('🔍 Cek status ujian:', { forceExit, hasFinishedExam, isSubmittingExam });

            if (forceExit === 'true' && !hasFinishedExam && !isSubmittingExam && !isResetting) {
                console.log('🔄 Deteksi keluar paksa sebelumnya - Mereset ujian...');

                // Tampilkan peringatan
                showSecurityWarning('⚠️ Terdeteksi keluar dari ujian! Ujian akan di-reset.');

                // Reset ujian
                resetExamAndRedirect();
            } else {
                console.log('✅ Tidak ada deteksi keluar paksa, ujian normal');
                // Hapus flag jika tidak ada masalah
                localStorage.removeItem(`exam_${EXAM_ID}_force_exit`);
            }
        });

        // 3. DETEKSI PAGEHIDE (alternatif untuk beberapa browser)
        window.addEventListener('pagehide', function() {
            if (!isSubmittingExam && !hasFinishedExam && !isResetting) {
                localStorage.setItem(`exam_${EXAM_ID}_force_exit`, 'true');
                localStorage.setItem(`exam_${EXAM_ID}_exit_time`, new Date().toISOString());
                console.log('📄 Pagehide terdeteksi');
            }
        });

        // 4. DETEKSI BEFOREUNLOAD (refresh/tutup tab)
        window.addEventListener('beforeunload', function(e) {
            if (!isSubmittingExam && !hasFinishedExam && !isResetting) {
                localStorage.setItem(`exam_${EXAM_ID}_force_exit`, 'true');
                localStorage.setItem(`exam_${EXAM_ID}_exit_time`, new Date().toISOString());

                const answeredCount = Object.keys(answers || {}).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
                const totalQ = {{ $questions->count() }};

                if (answeredCount !== totalQ && totalQ > 0) {
                    const message = '⚠️ PERINGATAN UJIAN! ⚠️\n\nAnda masih dalam ujian!\nJangan refresh atau tutup halaman!\n\n' +
                                   (totalQ - answeredCount) + ' soal belum dijawab.\n\nUJIAN AKAN DI-RESET JIKA ANDA KELUAR!';
                    e.preventDefault();
                    e.returnValue = message;
                    return message;
                }
            }
        });

        // ============ FITUR KEAMANAN LAINNYA ============

        function showSecurityWarning(message) {
            const warning = document.getElementById('securityWarning');
            warning.textContent = message || '⚠️ DILARANG KELUAR DARI UJIAN!';
            warning.classList.add('show');
            setTimeout(() => {
                warning.classList.remove('show');
            }, 3000);
        }

        function showBlockOverlay() {
            const overlay = document.getElementById('blockOverlay');
            overlay.style.display = 'flex';

            exitCount++;
            localStorage.setItem(`exam_${EXAM_ID}_exit_count`, exitCount);
            localStorage.setItem(`exam_${EXAM_ID}_force_exit`, 'true');

            fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/security-violation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    violation_type: 'exit_attempt',
                    timestamp: new Date().toISOString(),
                    attempt_count: blockCount + 1
                })
            }).catch(err => console.error('Error:', err));
        }

        function hideBlockOverlayAndReload() {
            document.getElementById('blockOverlay').style.display = 'none';
            window.location.reload();
        }

        // CEGAH TOMBOL BACK
        (function aggressiveBackPrevention() {
            for(let i = 0; i < 50; i++) {
                history.pushState(null, null, location.href);
            }

            window.addEventListener('popstate', function(e) {
                if (!isSubmittingExam && !hasFinishedExam && !isResetting) {
                    e.preventDefault();
                    e.stopPropagation();

                    backPressCount++;
                    showSecurityWarning(`🚫 Tombol BACK DINONAKTIFKAN! (Percobaan ke-${backPressCount})`);
                    showBlockOverlay();

                    for(let i = 0; i < 50; i++) {
                        history.pushState(null, null, location.href);
                    }

                    return false;
                }
            });
        })();

        // CEGAH GESTURE BACK
        let touchStartX = 0;
        let touchStartY = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: false });

        document.addEventListener('touchmove', function(e) {
            const touchCurrentX = e.touches[0].clientX;
            const touchCurrentY = e.touches[0].clientY;
            const deltaX = touchCurrentX - touchStartX;
            const deltaY = Math.abs(touchCurrentY - touchStartY);

            if (touchStartX < 50 && deltaX > 50 && deltaY < 100 && !isSubmittingExam && !hasFinishedExam && !isResetting) {
                e.preventDefault();
                e.stopPropagation();
                showSecurityWarning('🚫 Gesture kembali DINONAKTIFKAN!');
                showBlockOverlay();
                return false;
            }
        }, { passive: false });

        // CEGAH PULL TO REFRESH
        let startY = 0;

        document.addEventListener('touchstart', function(e) {
            startY = e.touches[0].pageY;
        }, { passive: false });

        document.addEventListener('touchmove', function(e) {
            const currentY = e.touches[0].pageY;
            const scrollTop = document.querySelector('.exam-container').scrollTop;

            if (scrollTop === 0 && currentY > startY + 15 && !isSubmittingExam && !hasFinishedExam && !isResetting) {
                e.preventDefault();
                showSecurityWarning('🚫 Pull-to-refresh DINONAKTIFKAN!');
                return false;
            }
        }, { passive: false });

        // CEGAH MENU KONTEKS
        document.addEventListener('contextmenu', function(e) {
            if (!isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                return false;
            }
        });

        // CEGAH COPY-PASTE
        document.addEventListener('copy', function(e) {
            if (e.target.tagName !== 'TEXTAREA' && !isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                showSecurityWarning('❌ Menyalin teks tidak diizinkan!');
                return false;
            }
        });

        document.addEventListener('paste', function(e) {
            if (e.target.tagName !== 'TEXTAREA' && !isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                showSecurityWarning('❌ Menempel teks tidak diizinkan!');
                return false;
            }
        });

        // LOCK ORIENTATION
        function lockOrientation() {
            if (screen.orientation && screen.orientation.lock) {
                screen.orientation.lock('portrait').catch(() => {});
            }
        }

        if (!hasFinishedExam) {
            lockOrientation();
        }

        // CEGAH KEYBOARD SHORTCUT
        document.addEventListener('keydown', function(e) {
            const dangerousKeys = ['F5', 'F12', 'Escape', 'Home', 'End'];
            const dangerousCombos = [
                { ctrl: true, key: 'r' }, { ctrl: true, key: 'R' },
                { ctrl: true, key: 'w' }, { ctrl: true, key: 'W' },
                { ctrl: true, key: 't' }, { ctrl: true, key: 'T' },
                { ctrl: true, shift: true, key: 'I' },
                { ctrl: true, shift: true, key: 'C' },
                { ctrl: true, shift: true, key: 'J' }
            ];

            if (dangerousKeys.includes(e.key)) {
                e.preventDefault();
                showSecurityWarning('🚫 Shortcut keyboard dinonaktifkan!');
                return false;
            }

            for (let combo of dangerousCombos) {
                if ((!combo.ctrl || e.ctrlKey) && (!combo.shift || e.shiftKey) && e.key === combo.key) {
                    e.preventDefault();
                    showSecurityWarning('🚫 Shortcut keyboard dinonaktifkan!');
                    return false;
                }
            }

            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                e.preventDefault();
                currentIndex--;
                renderQuestion(currentIndex);
            } else if (e.key === 'ArrowRight' && currentIndex < totalQuestions - 1) {
                e.preventDefault();
                currentIndex++;
                renderQuestion(currentIndex);
            }
        });

        // OVERRIDE UNTUK KODULAR
        if (window.Android) {
            try {
                window.Android.onBackPressed = function() {
                    if (!isSubmittingExam && !hasFinishedExam && !isResetting) {
                        showSecurityWarning('🚫 Tombol back dinonaktifkan di Kodular!');
                        showBlockOverlay();
                        return true;
                    }
                    return false;
                };
            } catch(e) {}
        }

        // ============ KODE UJIAN UTAMA ============

        const durationMinutes = {{ $exam->duration }};
        const questions = @json($questionsData);
        const savedAnswers = @json($answers ?? []);
        const totalQuestions = questions.length;

        let currentIndex = 0;
        const answers = {};
        const bookmarked = {};

        // Load saved answers
        Object.keys(savedAnswers).forEach(questionId => {
            answers[questionId] = savedAnswers[questionId];
        });

        // Load bookmarks
        const savedBookmarks = localStorage.getItem(`exam_${EXAM_ID}_bookmarks`);
        if (savedBookmarks) {
            try {
                const bookmarks = JSON.parse(savedBookmarks);
                Object.keys(bookmarks).forEach(qId => {
                    if (bookmarks[qId]) bookmarked[qId] = true;
                });
            } catch(e) {}
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
            questionTextEl.innerHTML = q.text || 'Memuat soal...';
            optionsAreaEl.innerHTML = '';

            if (q.type === 'pilihan_ganda') {
                let optionsList = [];

                if (q.options) {
                    if (Array.isArray(q.options)) {
                        q.options.slice(0, 4).forEach((opt, idx) => {
                            const key = String.fromCharCode(65 + idx);
                            let value = typeof opt === 'string' ? opt : (opt.label || opt.text || String(opt));
                            optionsList.push({ key: key, value: value.trim() });
                        });
                    } else if (typeof q.options === 'object') {
                        const optionKeys = ['A', 'B', 'C', 'D'];
                        optionKeys.forEach((key) => {
                            if (q.options[key] !== undefined && q.options[key] !== null) {
                                let value = String(q.options[key] || '').trim();
                                if (value !== 'null' && value !== 'undefined') {
                                    optionsList.push({ key: key, value: value });
                                }
                            }
                        });
                    }
                }

                const requiredKeys = ['A', 'B', 'C', 'D'];
                requiredKeys.forEach((key) => {
                    if (!optionsList.find(opt => opt.key === key)) {
                        optionsList.push({ key: key, value: '' });
                    }
                });

                optionsList.sort((a, b) => a.key.localeCompare(b.key));

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
                        saveAnswer(q.id, option.key);
                        updateNavBox(index);
                        updateCounts();
                    });

                    const text = document.createElement('span');
                    text.className = 'flex-1 text-gray-900';
                    if (option.value && option.value.trim() !== '') {
                        text.textContent = option.key + '. ' + option.value;
                    } else {
                        text.textContent = option.key + '. (Tidak ada teks)';
                    }

                    wrapper.appendChild(radio);
                    wrapper.appendChild(text);
                    optionsAreaEl.appendChild(wrapper);
                });
            } else if (q.type === 'essay' || q.type === 'esai') {
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
                bookmarkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" fill="currentColor" stroke="none"/>';
            } else {
                bookmarkBtn.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-gray-300 hover:border-yellow-400 hover:bg-yellow-50 transition-colors';
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
            localStorage.setItem(`exam_${EXAM_ID}_bookmarks`, JSON.stringify(bookmarked));
            updateBookmarkButton();
            updateNavBox(currentIndex);
            updateBookmarkedCount();
        }

        function updateBookmarkedCount() {
            const count = Object.keys(bookmarked).filter(qId => bookmarked[qId]).length;
            bookmarkedCountEl.textContent = count;
        }

        function saveAnswer(questionId, answer) {
            fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/save-answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ question_id: questionId, answer: answer })
            }).catch(() => {});
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
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            if (confirm(`Anda akan menyelesaikan ujian.\n\nSoal terjawab: ${answered}/${totalQuestions}\n\nYakin ingin selesai?`)) {
                isSubmittingExam = true;
                hasFinishedExam = true;
                localStorage.setItem(`exam_${EXAM_ID}_finished`, 'true');
                localStorage.removeItem(`exam_${EXAM_ID}_force_exit`);
                document.getElementById('submitForm').submit();
            }
        });

        // TIMER COUNTDOWN
        const startedAt = new Date('{{ $examResult->started_at ? $examResult->started_at->toIso8601String() : now()->toIso8601String() }}');
        const durationSeconds = durationMinutes * 60;
        const endTime = new Date(startedAt.getTime() + (durationSeconds * 1000));
        let timerInterval = null;

        function renderTimer() {
            const now = new Date();
            const remaining = Math.max(0, Math.floor((endTime - now) / 1000));
            const h = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const m = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            timerDisplay.textContent = `${h}:${m}:${s}`;

            if (remaining <= 0) {
                clearInterval(timerInterval);
                alert('Waktu habis! Ujian akan diselesaikan.');
                document.getElementById('submitForm').submit();
            }
        }

        timerInterval = setInterval(renderTimer, 1000);
        renderTimer();

        // INITIALIZE
        buildNavGrid();
        renderQuestion(0);
        updateCounts();
        updateBookmarkedCount();

        console.log('✅ Mode keamanan MOBILE TOTAL dengan AUTO-RESET aktif');
        console.log(`📊 Exit count sebelumnya: ${exitCount}`);
    </script>
</body>
</html>
