<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Ujian: {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            overflow-x: hidden;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Prevent pull-to-refresh */
        body {
            overscroll-behavior-y: contain;
        }

        /* Fullscreen mode indicator */
        #fullscreenIndicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 11px;
            z-index: 9999;
            pointer-events: none;
            font-family: monospace;
        }

        /* Disable text selection on buttons */
        button, .no-select {
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header dengan Timer -->
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
            <!-- Question Column (70%) -->
            <div class="flex-1 lg:w-2/3">
                <div class="p-6 bg-white rounded-lg shadow-lg">
                    <div id="questionArea" class="space-y-6">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="flex items-center flex-1 gap-3">
                                <span class="inline-flex items-center justify-center w-10 h-10 text-lg font-bold text-white rounded-lg bg-primary" id="currentNumber">1</span>
                                <h2 class="flex-1 text-xl font-semibold text-gray-900" id="questionText">Memuat soal...</h2>
                            </div>
                            <button id="bookmarkBtn" type="button" class="inline-flex items-center justify-center w-10 h-10 transition-colors border-2 border-gray-300 rounded-lg hover:border-yellow-400 hover:bg-yellow-50" title="Tandai soal untuk ditinjau kembali">
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

            <!-- Navigation Sidebar (30%) -->
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

    <!-- Finish Confirmation Modal -->
    <div id="finishConfirmModal" class="fixed inset-0 z-50 hidden overflow-y-auto transition-opacity duration-200 opacity-0" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="hideFinishConfirmModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div id="modalContent" class="inline-block w-full overflow-hidden text-left align-bottom transition-all transform scale-95 bg-white shadow-2xl rounded-2xl sm:my-8 sm:align-middle sm:max-w-lg">
                <div class="px-6 py-5 bg-gradient-to-r from-red-600 to-red-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 rounded-full bg-white/20">
                                <svg class="text-white w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white" id="modal-title">Konfirmasi Penyelesaian Ujian</h3>
                        </div>
                        <button onclick="hideFinishConfirmModal()" class="transition-colors text-white/80 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6 bg-white">
                    <p class="mb-6 text-gray-700">Anda yakin ingin menyelesaikan ujian ini? Pastikan semua jawaban sudah benar.</p>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 border-2 border-blue-200 bg-blue-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span class="text-sm font-medium text-blue-600">Total Soal</span>
                            </div>
                            <p class="text-2xl font-bold text-blue-900" id="modalTotalQuestions">0</p>
                        </div>

                        <div class="p-4 border-2 border-green-200 bg-green-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-green-600">Sudah Dikerjakan</span>
                            </div>
                            <p class="text-2xl font-bold text-green-900" id="modalAnswered">0</p>
                        </div>

                        <div class="p-4 border-2 border-yellow-200 bg-yellow-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-sm font-medium text-yellow-600">Ditandai</span>
                            </div>
                            <p class="text-2xl font-bold text-yellow-900" id="modalBookmarked">0</p>
                        </div>

                        <div class="p-4 border-2 border-gray-200 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-600">Belum Dikerjakan</span>
                            </div>
                            <p class="text-2xl font-bold text-gray-900" id="modalUnanswered">0</p>
                        </div>
                    </div>

                    <div id="modalWarning" class="mb-6"></div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50">
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

    <!-- Fullscreen Indicator -->
    <div id="fullscreenIndicator" style="display: none;">
        🔒 Mode Ujian Aktif
    </div>

    <script>
        // ============ FITUR PENGAMANAN UJIAN ============

        // Variabel keamanan
        let isSubmittingExam = false;
        let hasFinishedExam = localStorage.getItem(`exam_{{ $exam->id }}_finished`) === 'true';
        let backButtonPressed = false;

        // 1. CEGAH TOMBOL BACK BROWSER
        (function preventBackButton() {
            history.pushState(null, null, location.href);
            window.addEventListener('popstate', function(event) {
                if (!isSubmittingExam && !hasFinishedExam) {
                    const confirmExit = confirm('⚠️ PERINGATAN! ⚠️\n\nAnda akan keluar dari halaman ujian.\n\nJika keluar:\n❌ Jawaban yang belum disimpan akan hilang\n⏰ Waktu ujian tetap berjalan\n📱 Anda harus memulai ulang dari awal\n\nApakah Anda yakin ingin keluar?');

                    if (confirmExit) {
                        hasFinishedExam = true;
                        localStorage.setItem(`exam_{{ $exam->id }}_finished`, 'true');
                        history.back();
                    } else {
                        history.pushState(null, null, location.href);
                    }
                } else {
                    history.pushState(null, null, location.href);
                }
            });
        })();

        // 2. CEGAH REFRESH/TUTUP TAB
        window.addEventListener('beforeunload', function(e) {
            // Hitung jawaban yang sudah diisi
            const answeredCount = Object.keys(answers || {}).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            const totalQ = {{ $questions->count() }};
            const isCompleted = answeredCount === totalQ;

            if (!isSubmittingExam && !hasFinishedExam && !isCompleted && totalQ > 0) {
                const message = '⚠️ PERINGATAN UJIAN ⚠️\n\nAnda sedang dalam ujian!\n\n• ' + (totalQ - answeredCount) + ' soal belum dijawab\n• Waktu akan terus berjalan\n• Data yang belum tersimpan akan hilang\n\nTekan BATAL untuk melanjutkan ujian.';
                e.preventDefault();
                e.returnValue = message;
                return message;
            }
        });

        // 3. DETEKSI GESTURE BACK DI HP (Swipe dari tepi kiri)
        let touchStartXPosition = 0;
        let touchStartYPosition = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartXPosition = e.changedTouches[0].screenX;
            touchStartYPosition = e.changedTouches[0].screenY;
        }, false);

        document.addEventListener('touchend', function(e) {
            const touchEndXPosition = e.changedTouches[0].screenX;
            const touchEndYPosition = e.changedTouches[0].screenY;
            const deltaX = touchEndXPosition - touchStartXPosition;
            const deltaY = Math.abs(touchEndYPosition - touchStartYPosition);

            // Deteksi swipe dari tepi kiri (x < 30) dengan gerakan ke kanan (deltaX > 80)
            // dan bukan gerakan vertikal yang dominan
            if (touchStartXPosition < 40 && deltaX > 70 && deltaY < 100 && !isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                const confirmExit = confirm('⚠️ Gesture kembali terdeteksi!\n\nAnda akan keluar dari ujian. Lanjutkan?');
                if (!confirmExit) {
                    // Cegah gesture back
                    e.stopPropagation();
                    return false;
                } else {
                    hasFinishedExam = true;
                    localStorage.setItem(`exam_{{ $exam->id }}_finished`, 'true');
                }
            }
        }, false);

        // 4. NONAKTIFKAN MENU KONTEKS (Long Press)
        document.addEventListener('contextmenu', function(e) {
            if (!isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                return false;
            }
        });

        // 5. NONAKTIFKAN INSPECT ELEMENT DAN DEV TOOLS
        document.addEventListener('keydown', function(e) {
            // Nonaktifkan F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, Ctrl+Shift+C
            if (e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
                (e.ctrlKey && e.key === 'u') ||
                (e.ctrlKey && e.key === 'U')) {
                e.preventDefault();
                return false;
            }

            // Nonaktifkan Ctrl+R (refresh) dan Ctrl+F5 (hard refresh)
            if ((e.ctrlKey && (e.key === 'r' || e.key === 'R')) ||
                (e.ctrlKey && e.shiftKey && (e.key === 'R'))) {
                e.preventDefault();
                alert('⚠️ Refresh tidak diizinkan selama ujian berlangsung!');
                return false;
            }
        });

        // 6. MODE LAYAR PENUH (Fullscreen)
        let isFullscreenActive = false;

        function requestFullscreen() {
            const docEl = document.documentElement;
            const requestMethod = docEl.requestFullscreen ||
                                 docEl.webkitRequestFullscreen ||
                                 docEl.msRequestFullscreen;

            if (requestMethod) {
                requestMethod.call(docEl);
            }
        }

        function exitFullscreen() {
            const exitMethod = document.exitFullscreen ||
                              document.webkitExitFullscreen ||
                              document.msExitFullscreen;
            if (exitMethod) {
                exitMethod.call(document);
            }
        }

        // Cek dan minta fullscreen saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                if (!hasFinishedExam && !isSubmittingExam) {
                    const enableFullscreen = confirm('🔒 MODE UJIAN AKTIF\n\nAktifkan mode layar penuh untuk pengalaman ujian terbaik dan mencegah keluar secara tidak sengaja?\n\n(Anda dapat menekan Batal jika tidak ingin)');
                    if (enableFullscreen) {
                        requestFullscreen();
                        document.getElementById('fullscreenIndicator').style.display = 'block';
                        setTimeout(() => {
                            document.getElementById('fullscreenIndicator').style.opacity = '0';
                            setTimeout(() => {
                                document.getElementById('fullscreenIndicator').style.display = 'none';
                            }, 2000);
                        }, 3000);
                    }
                }
            }, 500);
        });

        // Cegah keluar dari fullscreen dengan ESC (kecuali user mengkonfirmasi)
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement && !document.webkitFullscreenElement && !hasFinishedExam && !isSubmittingExam) {
                // Jika keluar dari fullscreen, tanyakan kembali
                setTimeout(() => {
                    const reenter = confirm('⚠️ Mode ujian memerlukan layar penuh!\n\nAktifkan kembali mode layar penuh untuk melanjutkan ujian?');
                    if (reenter && !hasFinishedExam && !isSubmittingExam) {
                        requestFullscreen();
                    }
                }, 100);
            }
        });

        // 7. CEGAH PULL-TO-REFRESH
        let startY = 0;
        document.addEventListener('touchstart', function(e) {
            startY = e.touches[0].pageY;
        });

        document.addEventListener('touchmove', function(e) {
            const currentY = e.touches[0].pageY;
            const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;

            // Jika di paling atas dan menarik ke bawah
            if (scrollTop === 0 && currentY > startY && !isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
            }
        }, { passive: false });

        // 8. NONAKTIFKAN COPY-PASTE di area ujian
        document.addEventListener('copy', function(e) {
            if (!isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                alert('❌ Menyalin teks tidak diizinkan selama ujian!');
                return false;
            }
        });

        document.addEventListener('cut', function(e) {
            if (!isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                alert('❌ Memotong teks tidak diizinkan selama ujian!');
                return false;
            }
        });

        document.addEventListener('paste', function(e) {
            // Izinkan paste hanya di textarea untuk essay
            if (e.target.tagName !== 'TEXTAREA' && !isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                alert('❌ Menempel teks hanya diizinkan pada jawaban essay!');
                return false;
            }
        });

        // 9. Tandai ujian selesai saat submit
        const originalSubmit = document.getElementById('submitForm').submit;
        document.getElementById('submitForm').submit = function() {
            isSubmittingExam = true;
            hasFinishedExam = true;
            localStorage.setItem(`exam_{{ $exam->id }}_finished`, 'true');
            localStorage.setItem(`exam_{{ $exam->id }}_completed_at`, new Date().toISOString());
            return originalSubmit.apply(this, arguments);
        };

        // 10. DETEKSI BUKA DI TAB BARU
        (function detectNewTab() {
            const pageLoadTime = sessionStorage.getItem(`exam_{{ $exam->id }}_load_time`);
            if (pageLoadTime && !hasFinishedExam && !isSubmittingExam) {
                const timeDiff = Date.now() - parseInt(pageLoadTime);
                if (timeDiff > 5000) { // Lebih dari 5 detik
                    alert('⚠️ PERINGATAN: Deteksi pembukaan halaman di tab/window baru!\n\nUjian akan dilanjutkan, namun harap tidak membuka tab lain.');
                }
            }
            sessionStorage.setItem(`exam_{{ $exam->id }}_load_time`, Date.now().toString());
        })();

        // 11. LOCK ORIENTATION (Mencegah rotasi layar di HP)
        function lockOrientation() {
            if (screen.orientation && screen.orientation.lock) {
                screen.orientation.lock('portrait').catch(function(error) {
                    console.log('Orientation lock not supported:', error);
                });
            }
        }

        if (!hasFinishedExam && !isSubmittingExam) {
            lockOrientation();
        }

        console.log('✅ Mode keamanan ujian diaktifkan');
        console.log('📱 Fitur yang aktif:');
        console.log('   - Cegah tombol back');
        console.log('   - Cegah refresh/tutup tab');
        console.log('   - Cegah gesture back HP');
        console.log('   - Nonaktifkan menu konteks');
        console.log('   - Nonaktifkan DevTools');
        console.log('   - Mode layar penuh');
        console.log('   - Cegah pull-to-refresh');
        console.log('   - Nonaktifkan copy-paste');
        console.log('   - Deteksi tab baru');
        console.log('   - Lock orientation');

        // ============ KODE UJIAN UTAMA ============

        // Exam data
        const examId = {{ $exam->id }};
        const durationMinutes = {{ $exam->duration }};
        const questions = @json($questionsData);
        const savedAnswers = @json($answers ?? []);
        const totalQuestions = questions.length;

        // Debug: Log all questions data
        console.log('All questions data:', questions);

        let currentIndex = 0;
        const answers = {};
        const bookmarked = {};

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
            questionTextEl.innerHTML = q.text || 'Memuat soal...';
            optionsAreaEl.innerHTML = '';

            if (q.type === 'pilihan_ganda') {
                let optionsList = [];

                if (q.options) {
                    if (Array.isArray(q.options)) {
                        q.options.slice(0, 4).forEach((opt, idx) => {
                            const key = String.fromCharCode(65 + idx);
                            let value = '';
                            if (typeof opt === 'string') {
                                value = opt;
                            } else if (opt && typeof opt === 'object') {
                                value = opt.label || opt.text || opt.value || String(opt);
                            } else {
                                value = String(opt);
                            }
                            optionsList.push({ key: key, value: value.trim() });
                        });
                    } else if (typeof q.options === 'object' && q.options !== null) {
                        const optionKeys = ['A', 'B', 'C', 'D'];
                        optionKeys.forEach((key) => {
                            if ((q.options.hasOwnProperty && q.options.hasOwnProperty(key)) ||
                                (key in q.options) ||
                                (q.options[key] !== undefined && q.options[key] !== null)) {
                                let value = String(q.options[key] || '').trim();
                                if (value === 'null' || value === 'undefined') {
                                    value = '';
                                }
                                optionsList.push({ key: key, value: value });
                            }
                        });

                        if (optionsList.length === 0) {
                            Object.keys(q.options).forEach((key, idx) => {
                                if (idx >= 4) return;
                                const value = String(q.options[key] || '').trim();
                                if (/^[A-D]$/i.test(key)) {
                                    if (value !== 'null' && value !== 'undefined') {
                                        optionsList.push({ key: key.toUpperCase(), value: value });
                                    }
                                } else {
                                    const optionKey = String.fromCharCode(65 + idx);
                                    if (value !== 'null' && value !== 'undefined') {
                                        optionsList.push({ key: optionKey, value: value });
                                    }
                                }
                            });
                        }

                        const existingKeys = optionsList.map(opt => opt.key);
                        optionKeys.forEach((key) => {
                            if (!existingKeys.includes(key)) {
                                optionsList.push({ key: key, value: '' });
                            }
                        });

                        optionsList.sort((a, b) => a.key.localeCompare(b.key));
                    }
                }

                const requiredKeys = ['A', 'B', 'C', 'D'];
                const existingKeys = optionsList.map(opt => opt.key);
                requiredKeys.forEach((key) => {
                    if (!existingKeys.includes(key)) {
                        optionsList.push({ key: key, value: '' });
                    }
                });

                optionsList.sort((a, b) => a.key.localeCompare(b.key));
                const hasAnyContent = optionsList.some(opt => opt.value && opt.value.trim() !== '');
                const filteredOptions = optionsList
                    .filter(opt => ['A', 'B', 'C', 'D'].includes(opt.key))
                    .filter(opt => opt.value.trim() !== '' || hasAnyContent || optionsList.length === 1);

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
                        if (option.value && option.value.trim() !== '') {
                            text.textContent = option.key + '. ' + option.value;
                        } else {
                            text.textContent = option.key + '. (Tidak ada teks)';
                            text.className += ' text-gray-400 italic';
                        }

                        wrapper.appendChild(radio);
                        wrapper.appendChild(text);
                        optionsAreaEl.appendChild(wrapper);
                    });
                } else {
                    const message = document.createElement('div');
                    message.className = 'p-4 bg-red-50 border border-red-200 rounded-lg';
                    message.innerHTML = '<p class="text-sm font-medium text-red-600">Opsi tidak tersedia untuk soal pilihan ganda ini.</p>';
                    optionsAreaEl.appendChild(message);
                }
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
            } else {
                const message = document.createElement('div');
                message.className = 'p-4 bg-yellow-50 border border-yellow-200 rounded-lg';
                message.innerHTML = `<p class="text-sm font-medium text-yellow-800">Jenis soal tidak dikenali: "${q.type}"</p>`;
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

            if (isBookmarked && !isActive) {
                let star = btn.querySelector('.bookmark-star');
                if (!star) {
                    star = document.createElement('div');
                    star.className = 'bookmark-star absolute top-0 right-0 w-3 h-3';
                    star.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-yellow-500"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>';
                    btn.appendChild(star);
                }
            } else {
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

            document.getElementById('modalTotalQuestions').textContent = totalQuestions;
            document.getElementById('modalAnswered').textContent = answered;
            document.getElementById('modalUnanswered').textContent = unanswered;
            document.getElementById('modalBookmarked').textContent = bookmarkedCount;

            const warningEl = document.getElementById('modalWarning');
            if (unanswered > 0) {
                warningEl.classList.remove('hidden');
                warningEl.innerHTML = `<div class="flex items-center gap-2 text-yellow-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span class="font-medium">Masih ada ${unanswered} soal yang belum dikerjakan.</span></div>`;
            } else {
                warningEl.classList.add('hidden');
            }

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
            isSubmittingExam = true;
            hasFinishedExam = true;
            localStorage.setItem(`exam_${examId}_finished`, 'true');
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
                return;
            }

            const now = new Date();
            const remaining = Math.max(0, Math.floor((endTime - now) / 1000));

            const h = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const m = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');

            timerDisplay.textContent = `${h}:${m}:${s}`;

            const timerBox = document.getElementById('timerBox');
            if (remaining <= 300 && remaining > 0) {
                timerBox.className = 'flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg shadow border-2 border-red-800 animate-pulse';
            } else if (remaining <= 600 && remaining > 0) {
                timerBox.className = 'flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg shadow border-2 border-orange-800';
            } else if (remaining > 600) {
                timerBox.className = 'flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg shadow border-2 border-red-600';
            }

            if (remaining <= 0 && !hasSubmitted) {
                hasSubmitted = true;
                if (timerInterval) {
                    clearInterval(timerInterval);
                }

                Object.keys(answers).forEach(questionId => {
                    if (answers[questionId]) {
                        saveAnswer(questionId, answers[questionId]);
                    }
                });

                setTimeout(() => {
                    isSubmittingExam = true;
                    hasFinishedExam = true;
                    document.getElementById('submitForm').submit();
                }, 500);
            }
        }

        timerInterval = setInterval(renderTimer, 1000);
        renderTimer();

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'TEXTAREA') return;

            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                currentIndex--;
                renderQuestion(currentIndex);
            } else if (e.key === 'ArrowRight' && currentIndex < totalQuestions - 1) {
                currentIndex++;
                renderQuestion(currentIndex);
            } else if ((e.key === ' ' || e.key === 'b' || e.key === 'B') && e.target.tagName !== 'BUTTON') {
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
