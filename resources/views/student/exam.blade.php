<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Ujian Online - Mode Aman</title>
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
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        /* Prevent pull-to-refresh */
        body {
            overscroll-behavior-y: contain;
        }

        /* Sembunyikan scrollbar tapi tetap bisa scroll */
        body::-webkit-scrollbar {
            display: none;
        }

        /* Container utama full height */
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

        /* Disable text selection on buttons */
        button, .no-select {
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        /* Peringatan floating */
        #securityWarning {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(220, 38, 38, 0.95);
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 13px;
            font-weight: 500;
            z-index: 10000;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        #securityWarning.show {
            transform: translateY(0);
        }

        /* Overlay blokir */
        #blockOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.92);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
            padding: 20px;
            backdrop-filter: blur(8px);
        }

        /* Loading overlay (opsional) */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            flex-direction: column;
            gap: 20px;
        }

        .animate-pulse {
            animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* custom scroll */
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Security Warning -->
    <div id="securityWarning">
        ⚠️ DILARANG KELUAR DARI APLIKASI UJIAN! Jawaban akan tersimpan otomatis.
    </div>

    <!-- Block Overlay (jika mencoba keluar) -->
    <div id="blockOverlay">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h2 style="margin: 20px 0 10px;">🚫 AKSES DITOLAK!</h2>
        <p>Anda tidak diizinkan keluar dari aplikasi ujian.<br>Silakan lanjutkan mengerjakan soal.</p>
        <button onclick="hideBlockOverlay()" style="margin-top: 20px; padding: 10px 30px; background: #4f46e5; border: none; border-radius: 8px; color: white; font-weight: bold; cursor: pointer;">Kembali ke Ujian</button>
    </div>

    <div class="exam-container" id="examContainer">
        <!-- Header dengan Timer -->
        <header class="sticky top-0 z-50 bg-white shadow-md" style="position: sticky; top:0; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div class="flex items-center justify-between px-4 py-3 mx-auto max-w-7xl">
                <div>
                    <h1 class="text-lg font-bold text-gray-900" id="examTitle">Ujian: Matematika Dasar</h1>
                    <p class="text-sm text-gray-600" id="examMeta">Matematika | Durasi: 60 menit</p>
                </div>
                <div id="timerBox" class="flex items-center gap-2 px-4 py-2 text-white bg-orange-600 border-2 border-red-600 rounded-lg shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        <path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zm0 2.25a7.5 7.5 0 110 15 7.5 7.5 0 010-15z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-mono text-lg font-semibold" id="timerDisplay">01:00:00</span>
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
                                    <span class="inline-flex items-center justify-center w-10 h-10 text-lg font-bold text-white bg-indigo-600 rounded-lg" id="currentNumber">1</span>
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
                                <button id="nextBtn" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors font-medium">Soal Selanjutnya</button>
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

                        <div id="navGrid" class="grid grid-cols-5 gap-2 mb-6 overflow-y-auto max-h-96 custom-scroll"></div>

                        <div class="mt-auto">
                            <form id="submitForm" method="POST" action="#" onsubmit="event.preventDefault(); confirmFinishExam();">
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
                    <button onclick="hideFinishConfirmModal()" class="px-5 py-2.5 rounded-lg font-medium text-gray-700 bg-white border-2 border-gray-300 hover:bg-gray-50 transition-colors">Batal</button>
                    <button onclick="confirmFinishExam()" class="px-5 py-2.5 rounded-lg font-medium text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">Ya, Selesaikan Ujian</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==================== DATA UJIAN SIMULASI (STATIS UNTUK DEMO) ====================
        const examData = {
            id: 101,
            title: "Ujian Akhir Matematika",
            subject: "Matematika",
            duration: 60, // menit
            started_at: new Date() // waktu mulai sekarang
        };

        // Kumpulan soal (pilihan ganda + essay)
        const questionsData = [
            { id: 1, type: "pilihan_ganda", text: "Hasil dari 25 + 17 adalah?", options: { A: "40", B: "42", C: "38", D: "45" } },
            { id: 2, type: "pilihan_ganda", text: "Akar kuadrat dari 144 adalah?", options: { A: "10", B: "11", C: "12", D: "13" } },
            { id: 3, type: "pilihan_ganda", text: "Jika x = 5, maka 3x + 7 = ?", options: { A: "20", B: "22", C: "18", D: "25" } },
            { id: 4, type: "essay", text: "Jelaskan pengertian bilangan prima dan berikan contohnya!" },
            { id: 5, type: "pilihan_ganda", text: "Luas lingkaran dengan jari-jari 7 cm adalah? (π=22/7)", options: { A: "154 cm²", B: "144 cm²", C: "164 cm²", D: "174 cm²" } }
        ];
        const totalQ = questionsData.length;

        // Menampilkan data ke header
        document.getElementById('examTitle').innerText = `Ujian: ${examData.title}`;
        document.getElementById('examMeta').innerHTML = `${examData.subject} | Durasi: ${examData.duration} menit`;
        document.getElementById('totalQuestions').innerText = totalQ;

        // State ujian
        let currentIndex = 0;
        let answers = {};      // { questionId: jawaban }
        let bookmarked = {};   // { questionId: true }
        let isSubmittingExam = false;
        let hasFinishedExam = false;
        let timerInterval = null;
        let blockCount = 0;

        // Load dari localStorage jika ada
        const savedAnswers = localStorage.getItem(`exam_${examData.id}_answers`);
        if (savedAnswers) {
            try {
                answers = JSON.parse(savedAnswers);
            } catch(e) {}
        }
        const savedBookmarks = localStorage.getItem(`exam_${examData.id}_bookmarks`);
        if (savedBookmarks) {
            try {
                bookmarked = JSON.parse(savedBookmarks);
            } catch(e) {}
        }

        // DOM elements
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

        // Helper update counts
        function updateCounts() {
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            const unanswered = totalQ - answered;
            answeredCountEl.textContent = answered;
            unansweredCountEl.textContent = unanswered;
            const bookmarkedCount = Object.keys(bookmarked).filter(b => bookmarked[b]).length;
            bookmarkedCountEl.textContent = bookmarkedCount;
        }

        // Simpan jawaban ke localStorage (simulasi)
        function saveAnswerLocally(questionId, answer) {
            answers[questionId] = answer;
            localStorage.setItem(`exam_${examData.id}_answers`, JSON.stringify(answers));
            updateCounts();
        }

        // Render soal berdasarkan index
        function renderQuestion(index) {
            if (index < 0 || index >= totalQ) return;
            const q = questionsData[index];
            currentNumberEl.textContent = index + 1;
            questionTextEl.innerHTML = q.text;
            optionsAreaEl.innerHTML = '';

            if (q.type === 'pilihan_ganda') {
                const opts = q.options;
                const optionKeys = ['A', 'B', 'C', 'D'];
                optionKeys.forEach(key => {
                    const value = opts[key] || '';
                    const wrapper = document.createElement('label');
                    wrapper.className = 'flex items-center gap-3 px-4 py-3 rounded-lg border-2 border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 cursor-pointer transition-all';
                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = `q_${q.id}`;
                    radio.value = key;
                    radio.checked = answers[q.id] === key;
                    radio.addEventListener('change', () => {
                        answers[q.id] = key;
                        saveAnswerLocally(q.id, key);
                        updateNavBox(index);
                        updateCounts();
                    });
                    const textSpan = document.createElement('span');
                    textSpan.className = 'flex-1 text-gray-900';
                    textSpan.textContent = `${key}. ${value || '(Tidak ada teks)'}`;
                    wrapper.appendChild(radio);
                    wrapper.appendChild(textSpan);
                    optionsAreaEl.appendChild(wrapper);
                });
            } else if (q.type === 'essay') {
                const textarea = document.createElement('textarea');
                textarea.className = 'w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent';
                textarea.rows = 6;
                textarea.placeholder = 'Tulis jawaban essay Anda di sini...';
                textarea.value = answers[q.id] || '';
                textarea.addEventListener('input', () => {
                    answers[q.id] = textarea.value;
                    saveAnswerLocally(q.id, textarea.value);
                    updateNavBox(index);
                    updateCounts();
                });
                optionsAreaEl.appendChild(textarea);
            } else {
                optionsAreaEl.innerHTML = '<div class="p-4 text-red-700 rounded bg-red-50">Jenis soal tidak dikenal</div>';
            }

            updateActiveBox(index);
            updatePrevNextState();
            updateBookmarkButton();
        }

        function updateBookmarkButton() {
            const q = questionsData[currentIndex];
            const isBookmarked = bookmarked[q.id] || false;
            if (isBookmarked) {
                bookmarkBtn.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-yellow-400 bg-yellow-50';
                bookmarkIcon.className = 'w-5 h-5 text-yellow-600';
                bookmarkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" fill="currentColor" stroke="none"/>';
            } else {
                bookmarkBtn.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-gray-300 hover:border-yellow-400 hover:bg-yellow-50';
                bookmarkIcon.className = 'w-5 h-5 text-gray-400';
                bookmarkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>';
            }
        }

        function toggleBookmark() {
            const q = questionsData[currentIndex];
            if (bookmarked[q.id]) delete bookmarked[q.id];
            else bookmarked[q.id] = true;
            localStorage.setItem(`exam_${examData.id}_bookmarks`, JSON.stringify(bookmarked));
            updateBookmarkButton();
            updateNavBox(currentIndex);
            updateCounts();
        }

        function updatePrevNextState() {
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex === totalQ - 1;
            prevBtn.className = `px-6 py-2.5 rounded-lg font-medium transition-colors ${prevBtn.disabled ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-300 text-gray-900 hover:bg-gray-400'}`;
            nextBtn.className = `px-6 py-2.5 rounded-lg font-medium transition-colors ${nextBtn.disabled ? 'bg-indigo-400 text-white cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700'}`;
        }

        function buildNavGrid() {
            navGridEl.innerHTML = '';
            for (let i = 0; i < totalQ; i++) {
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
            const q = questionsData[i];
            const isAnswered = answers[q.id] && answers[q.id].trim() !== '';
            const isBookmarkedVal = bookmarked[q.id] || false;
            const isActive = i === currentIndex;

            let className = 'w-12 h-12 rounded-lg flex items-center justify-center text-sm font-semibold border-2 transition-all relative';
            if (isActive) className += ' bg-indigo-600 text-white border-indigo-700 shadow-md';
            else if (isAnswered && isBookmarkedVal) className += ' bg-green-500 text-white border-green-600';
            else if (isAnswered) className += ' bg-green-500 text-white border-green-600';
            else if (isBookmarkedVal) className += ' bg-yellow-100 text-yellow-900 border-yellow-400';
            else className += ' bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
            btn.className = className;

            // hapus star lama
            const oldStar = btn.querySelector('.bookmark-star');
            if (oldStar) oldStar.remove();
            if (isBookmarkedVal && !isActive) {
                const star = document.createElement('div');
                star.className = 'bookmark-star absolute top-0 right-0 w-3 h-3';
                star.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-yellow-500"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>';
                btn.appendChild(star);
            }
        }

        function updateActiveBox(i) {
            for (let idx = 0; idx < navGridEl.children.length; idx++) updateNavBox(idx);
        }

        // Timer logic
        const startedAt = examData.started_at;
        const durationSeconds = examData.duration * 60;
        const endTime = new Date(startedAt.getTime() + durationSeconds * 1000);
        let hasSubmittedAuto = false;

        function renderTimer() {
            if (hasSubmittedAuto || isSubmittingExam) return;
            const now = new Date();
            const remaining = Math.max(0, Math.floor((endTime - now) / 1000));
            const h = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const m = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            timerDisplay.textContent = `${h}:${m}:${s}`;
            const timerBox = document.getElementById('timerBox');
            if (remaining <= 300 && remaining > 0) timerBox.className = "flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg shadow border-2 border-red-800 animate-pulse";
            else if (remaining <= 600) timerBox.className = "flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg shadow border-2 border-orange-800";
            else timerBox.className = "flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg shadow border-2 border-red-600";
            if (remaining <= 0 && !hasSubmittedAuto) {
                hasSubmittedAuto = true;
                if (timerInterval) clearInterval(timerInterval);
                alert("Waktu habis! Ujian akan diselesaikan secara otomatis.");
                isSubmittingExam = true;
                hasFinishedExam = true;
                localStorage.setItem(`exam_${examData.id}_finished`, 'true');
                document.getElementById('submitForm').submit();
            }
        }

        timerInterval = setInterval(renderTimer, 1000);
        renderTimer();

        // Event listeners
        prevBtn.addEventListener('click', () => { if (currentIndex > 0) { currentIndex--; renderQuestion(currentIndex); } });
        nextBtn.addEventListener('click', () => { if (currentIndex < totalQ - 1) { currentIndex++; renderQuestion(currentIndex); } });
        bookmarkBtn.addEventListener('click', toggleBookmark);

        // Modal handling
        function showFinishConfirmModal() {
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            const unanswered = totalQ - answered;
            const bookmarkedCount = Object.keys(bookmarked).filter(b => bookmarked[b]).length;
            document.getElementById('modalTotalQuestions').textContent = totalQ;
            document.getElementById('modalAnswered').textContent = answered;
            document.getElementById('modalUnanswered').textContent = unanswered;
            document.getElementById('modalBookmarked').textContent = bookmarkedCount;
            const warningEl = document.getElementById('modalWarning');
            if (unanswered > 0) {
                warningEl.innerHTML = `<div class="flex items-center gap-2 text-yellow-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span class="font-medium">Masih ada ${unanswered} soal yang belum dikerjakan.</span></div>`;
            } else {
                warningEl.innerHTML = '';
            }
            document.getElementById('finishConfirmModal').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('finishConfirmModal').classList.remove('opacity-0');
                document.getElementById('finishConfirmModal').classList.add('opacity-100');
                document.getElementById('modalContent').classList.remove('scale-95');
                document.getElementById('modalContent').classList.add('scale-100');
            }, 10);
        }
        window.hideFinishConfirmModal = function() {
            document.getElementById('finishConfirmModal').classList.add('opacity-0');
            document.getElementById('modalContent').classList.remove('scale-100');
            document.getElementById('modalContent').classList.add('scale-95');
            setTimeout(() => document.getElementById('finishConfirmModal').classList.add('hidden'), 200);
        };
        window.confirmFinishExam = function() {
            hideFinishConfirmModal();
            isSubmittingExam = true;
            hasFinishedExam = true;
            localStorage.setItem(`exam_${examData.id}_finished`, 'true');
            alert("✅ Ujian selesai! Terima kasih. (Simulasi pengiriman jawaban)");
            console.log("Jawaban akhir:", answers);
            // optional: redirect atau tutup
        };
        finishBtn.addEventListener('click', showFinishConfirmModal);

        // ========== FITUR PENGAMANAN LENGKAP ==========
        function showSecurityWarning(msg) {
            const warn = document.getElementById('securityWarning');
            warn.textContent = msg || '⚠️ DILARANG KELUAR DARI APLIKASI UJIAN!';
            warn.classList.add('show');
            setTimeout(() => warn.classList.remove('show'), 3000);
        }
        window.hideBlockOverlay = function() { document.getElementById('blockOverlay').style.display = 'none'; };
        function showBlockOverlay() { document.getElementById('blockOverlay').style.display = 'flex'; setTimeout(() => hideBlockOverlay(), 3000); }
        // back button prevention
        history.pushState(null, null, location.href);
        window.addEventListener('popstate', function(e) {
            if (!isSubmittingExam && !hasFinishedExam) {
                showSecurityWarning('🚫 Tombol kembali dinonaktifkan! Lanjutkan ujian.');
                showBlockOverlay();
                history.pushState(null, null, location.href);
                blockCount++;
            } else { history.pushState(null, null, location.href); }
        });
        window.addEventListener('beforeunload', function(e) {
            const answered = Object.keys(answers).filter(qId => answers[qId] && answers[qId].trim() !== '').length;
            if (!isSubmittingExam && !hasFinishedExam && answered !== totalQ && totalQ > 0) {
                const msg = "⚠️ PERINGATAN UJIAN! Anda keluar akan mengganggu proses. Silakan lanjutkan ujian.";
                e.preventDefault();
                e.returnValue = msg;
                return msg;
            }
        });
        document.addEventListener('contextmenu', e => { if (!isSubmittingExam && !hasFinishedExam) { e.preventDefault(); showSecurityWarning('Menu konteks dinonaktifkan'); } });
        document.addEventListener('copy', e => { if (!isSubmittingExam && !hasFinishedExam) { e.preventDefault(); showSecurityWarning('Menyalin teks tidak diizinkan!'); } });
        document.addEventListener('cut', e => { if (!isSubmittingExam && !hasFinishedExam) { e.preventDefault(); showSecurityWarning('Memotong teks tidak diizinkan!'); } });
        // gesture back swipe
        let touchStartX = 0;
        document.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; });
        document.addEventListener('touchend', e => {
            const endX = e.changedTouches[0].screenX;
            const delta = endX - touchStartX;
            if (touchStartX < 40 && delta > 70 && !isSubmittingExam && !hasFinishedExam) {
                e.preventDefault();
                showSecurityWarning('Gesture kembali dinonaktifkan!');
                showBlockOverlay();
            }
        });
        // lock orientation
        if (screen.orientation && screen.orientation.lock && !hasFinishedExam) screen.orientation.lock('portrait').catch(()=>{});
        document.addEventListener('visibilitychange', () => { if(document.hidden && !isSubmittingExam && !hasFinishedExam) showSecurityWarning('Jangan tinggalkan aplikasi ujian!'); });
        // inisialisasi utama
        buildNavGrid();
        renderQuestion(0);
        updateCounts();
        setTimeout(() => showSecurityWarning('🔒 Mode keamanan aktif! Jangan keluar dari ujian.'), 500);
    </script>
</body>
</html>
