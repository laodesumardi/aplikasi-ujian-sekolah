<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>{{ $exam->title }} | Ujian Online</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f7fa;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1a1a2e;
        }

        /* LAYAR MULAI */
        .start-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f5f7fa;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }

        .start-card {
            background: white;
            border-radius: 32px;
            padding: 48px 40px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 35px -10px rgba(0,0,0,0.1);
        }

        .start-card h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #1a1a2e;
        }

        .start-card .meta {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e9ecef;
        }

        .start-card .warning-box {
            background: #fff3cd;
            border: 1px solid #ffecb3;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 32px;
            text-align: left;
        }

        .start-card .warning-box p {
            font-size: 13px;
            color: #856404;
            margin-bottom: 8px;
        }

        .btn-start {
            background: #2d6a4f;
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-start:hover {
            background: #1b4d3e;
        }

        /* KONTEN UJIAN */
        .exam-wrapper {
            display: none;
            min-height: 100vh;
            background: #f5f7fa;
        }

        /* HEADER */
        .exam-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .exam-info h2 {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .exam-info p {
            font-size: 13px;
            color: #6c757d;
        }

        .timer-card {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 60px;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .timer-display {
            font-family: monospace;
            font-size: 24px;
            font-weight: 700;
            color: #c53030;
        }

        /* MAIN CONTENT */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        @media (min-width: 992px) {
            .main-container {
                flex-direction: row;
                align-items: flex-start;
            }
            .question-panel { flex: 2; }
            .nav-panel { flex: 1; position: sticky; top: 90px; }
        }

        /* PANEL SOAL */
        .question-panel {
            background: white;
            border-radius: 24px;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .question-header {
            padding: 24px 28px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
        }

        .question-badge {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badge-number {
            background: #2d6a4f;
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
        }

        .question-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a2e;
            line-height: 1.4;
            flex: 1;
        }

        .bookmark-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            background: white;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.2s;
        }

        .bookmark-btn.active {
            background: #fff8e7;
            border-color: #f59e0b;
        }

        /* OPSI JAWABAN - SELURUH AREA BISA DIKLIK */
        .options-area {
            padding: 24px 28px;
        }

        .option-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .option-item:hover {
            border-color: #2d6a4f;
            background: #f8fafc;
        }

        .option-item.selected {
            border-color: #2d6a4f;
            background: #f0fdf4;
        }

        /* SEMBUNYIKAN RADIO DEFAULT */
        .option-radio {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* CUSTOM RADIO YANG LEBIH BAGUS */
        .custom-radio {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            background: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .option-item.selected .custom-radio {
            border-color: #2d6a4f;
            background: #2d6a4f;
            box-shadow: inset 0 0 0 4px white;
        }

        .option-item:hover .custom-radio {
            border-color: #2d6a4f;
        }

        .option-letter {
            font-weight: 700;
            color: #2d6a4f;
            min-width: 28px;
            font-size: 15px;
        }

        .option-text {
            flex: 1;
            color: #334155;
            line-height: 1.4;
        }

        .essay-area {
            width: 100%;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 16px;
            font-size: 15px;
            font-family: inherit;
            min-height: 200px;
            resize: vertical;
        }

        .essay-area:focus {
            outline: none;
            border-color: #2d6a4f;
        }

        /* NAVIGASI SOAL */
        .question-footer {
            padding: 20px 28px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }

        .nav-btn {
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .nav-prev {
            background: #e9ecef;
            color: #495057;
        }

        .nav-prev:hover:not(:disabled) {
            background: #dee2e6;
        }

        .nav-next {
            background: #2d6a4f;
            color: white;
        }

        .nav-next:hover:not(:disabled) {
            background: #1b4d3e;
        }

        .nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* PANEL NAVIGASI */
        .nav-panel {
            background: white;
            border-radius: 24px;
            border: 1px solid #e9ecef;
            padding: 24px;
        }

        .stats-grid {
            background: #f8fafc;
            border-radius: 20px;
            padding: 16px;
            margin-bottom: 24px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .stat-card {
            text-align: center;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
        }

        .stat-number.total { color: #1a1a2e; }
        .stat-number.answered { color: #2d6a4f; }
        .stat-number.bookmarked { color: #f59e0b; }
        .stat-number.unanswered { color: #e53e3e; }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 24px;
            max-height: 320px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .nav-item {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: 1px solid #e9ecef;
            background: white;
            color: #495057;
            transition: all 0.15s;
        }

        .nav-item.answered {
            background: #2d6a4f;
            color: white;
            border-color: #2d6a4f;
        }

        .nav-item.bookmarked {
            background: #fff8e7;
            border-color: #f59e0b;
            color: #d97706;
        }

        .nav-item.active {
            background: #1a1a2e;
            color: white;
            border-color: #1a1a2e;
            transform: scale(1.02);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 40px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #b91c1c;
        }

        /* MODAL */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-container {
            background: white;
            border-radius: 32px;
            padding: 32px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .modal-stats {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            margin: 20px 0;
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

        /* TOAST */
        .toast-message {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #1a1a2e;
            color: white;
            padding: 12px 24px;
            border-radius: 60px;
            font-size: 13px;
            z-index: 10001;
            transition: 0.25s;
            white-space: nowrap;
        }

        .toast-message.show {
            transform: translateX(-50%) translateY(0);
        }

        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #e9ecef;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<!-- LAYAR PEMBUKA -->
<div class="start-screen" id="startScreen">
    <div class="start-card">
        <h1>{{ $exam->title }}</h1>
        <div class="meta">{{ $exam->subject->name ?? 'Ujian Online' }} • {{ $exam->duration }} Menit</div>
        <div class="warning-box">
            <p>📌 <strong>Perhatian:</strong></p>
            <p>• Ujian akan berlangsung dalam mode layar penuh</p>
            <p>• Dilarang keluar dari aplikasi selama ujian</p>
            <p>• Jawaban akan tersimpan secara otomatis</p>
        </div>
        <button class="btn-start" id="startBtn">Mulai Ujian →</button>
    </div>
</div>

<!-- KONTEN UJIAN -->
<div class="exam-wrapper" id="examWrapper">
    <div class="exam-header">
        <div class="header-container">
            <div class="exam-info">
                <h2>{{ $exam->title }}</h2>
                <p>{{ $exam->subject->name ?? 'Ujian' }} • {{ $exam->duration }} menit</p>
            </div>
            <div class="timer-card">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <div class="timer-display" id="timerDisplay">00:00:00</div>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="question-panel">
            <div class="question-header">
                <div class="question-badge">
                    <div class="badge-number" id="currentNumber">1</div>
                </div>
                <div class="question-title" id="questionText">Memuat soal...</div>
                <button class="bookmark-btn" id="bookmarkBtn">☆</button>
            </div>

            <div class="options-area" id="optionsArea"></div>

            <div class="question-footer">
                <button class="nav-btn nav-prev" id="prevBtn">← Sebelumnya</button>
                <button class="nav-btn nav-next" id="nextBtn">Selanjutnya →</button>
            </div>
        </div>

        <div class="nav-panel">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number total" id="totalQuestions">0</div>
                    <div class="stat-label">Total Soal</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number answered" id="answeredCount">0</div>
                    <div class="stat-label">Dijawab</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number bookmarked" id="bookmarkedCount">0</div>
                    <div class="stat-label">Ditandai</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number unanswered" id="unansweredCount">0</div>
                    <div class="stat-label">Belum</div>
                </div>
            </div>

            <div class="nav-grid" id="navGrid"></div>

            <form id="submitForm" method="POST" action="{{ route('siswa.exam.submit', $exam->id) }}">
                @csrf
                <button type="button" class="btn-submit" id="finishBtn">Selesaikan Ujian</button>
            </form>
        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="finishModal">
    <div class="modal-container">
        <h3>Selesaikan Ujian?</h3>
        <p style="color: #6c757d;">Pastikan semua jawaban sudah benar</p>
        <div class="modal-stats" id="modalStats"></div>
        <div class="modal-buttons">
            <button style="background: #e9ecef;" id="cancelBtn">Kembali</button>
            <button style="background: #dc2626; color: white;" id="confirmBtn">Ya, Selesai</button>
        </div>
    </div>
</div>

<div class="toast-message" id="toast">⚠️ Dilarang keluar dari ujian!</div>

<script>
// ==================== DATA ====================
const EXAM_ID = {{ $exam->id }};
const DURASI_MENIT = {{ $exam->duration }};
const SOAL_ASLI = @json($questionsData);

let ujianDimulai = false;
let sedangSubmit = false;
let sudahSelesai = localStorage.getItem(`exam_${EXAM_ID}_finished`) === 'true';

let soal = [];
let indexSekarang = 0;
let jawaban = {};
let ditandai = {};

let timerInterval = null;

// ==================== FULL SCREEN ====================
function masukFullscreen() {
    const doc = document.documentElement;
    if (doc.requestFullscreen) doc.requestFullscreen();
    else if (doc.webkitRequestFullscreen) doc.webkitRequestFullscreen();
}

setInterval(() => {
    if (ujianDimulai && !sedangSubmit && !sudahSelesai) {
        const isFull = document.fullscreenElement || document.webkitFullscreenElement;
        if (!isFull) masukFullscreen();
    }
}, 3000);

// ==================== FUNGSI BANTUAN ====================
function acakArray(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
}

function acakSoal() {
    let acak = acakArray([...SOAL_ASLI]);
    acak = acak.map(q => {
        if (q.type === 'pilihan_ganda' && q.options) {
            let opts = [];
            if (Array.isArray(q.options)) opts = [...q.options];
            else if (typeof q.options === 'object') opts = Object.values(q.options);
            const acakOpt = acakArray([...opts]);
            const baru = {};
            ['A', 'B', 'C', 'D'].forEach((key, idx) => {
                if (acakOpt[idx]) baru[key] = acakOpt[idx];
            });
            return { ...q, options: baru };
        }
        return q;
    });
    return acak;
}

function simpanJawaban(id, jawab) {
    const semua = JSON.parse(localStorage.getItem(`exam_${EXAM_ID}_answers`) || '{}');
    semua[id] = jawab;
    localStorage.setItem(`exam_${EXAM_ID}_answers`, JSON.stringify(semua));

    fetch(`{{ url('/siswa/ujian') }}/${EXAM_ID}/save-answer`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ question_id: id, answer: jawab })
    }).catch(() => {});
}

function toast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2000);
}

// ==================== TIMER ====================
function mulaiTimer() {
    let waktuMulai = localStorage.getItem(`exam_${EXAM_ID}_start_time`);
    if (!waktuMulai || sudahSelesai) {
        waktuMulai = new Date().toISOString();
        localStorage.setItem(`exam_${EXAM_ID}_start_time`, waktuMulai);
    }

    const waktuAkhir = new Date(new Date(waktuMulai).getTime() + (DURASI_MENIT * 60 * 1000));

    function updateTimer() {
        if (sedangSubmit || sudahSelesai) return;

        const sisa = Math.max(0, Math.floor((waktuAkhir - new Date()) / 1000));
        const jam = Math.floor(sisa / 3600);
        const menit = Math.floor((sisa % 3600) / 60);
        const detik = sisa % 60;

        document.getElementById('timerDisplay').textContent =
            `${jam.toString().padStart(2,'0')}:${menit.toString().padStart(2,'0')}:${detik.toString().padStart(2,'0')}`;

        if (sisa <= 0) {
            clearInterval(timerInterval);
            alert('⏰ WAKTU HABIS!');
            document.getElementById('submitForm').submit();
        }
    }

    if (timerInterval) clearInterval(timerInterval);
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);
}

// ==================== RENDER SOAL ====================
function tampilSoal(index) {
    if (index < 0 || index >= soal.length) return;

    const q = soal[index];
    document.getElementById('currentNumber').textContent = index + 1;
    document.getElementById('questionText').innerHTML = q.text;

    const area = document.getElementById('optionsArea');
    area.innerHTML = '';

    if (q.type === 'pilihan_ganda') {
        let opts = [];
        if (q.options && typeof q.options === 'object') {
            ['A', 'B', 'C', 'D'].forEach(key => {
                if (q.options[key] && q.options[key] !== 'null') {
                    opts.push({ key, value: String(q.options[key]) });
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
            div.className = `option-item ${jawaban[q.id] === opt.key ? 'selected' : ''}`;

            // Radio tersembunyi (tetap ada untuk aksesibilitas)
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = `q_${q.id}`;
            radio.value = opt.key;
            radio.className = 'option-radio';
            radio.checked = jawaban[q.id] === opt.key;

            // Custom radio (yang terlihat)
            const customRadio = document.createElement('span');
            customRadio.className = 'custom-radio';

            const letter = document.createElement('span');
            letter.className = 'option-letter';
            letter.textContent = opt.key;

            const text = document.createElement('span');
            text.className = 'option-text';
            text.textContent = opt.value;

            div.appendChild(radio);
            div.appendChild(customRadio);
            div.appendChild(letter);
            div.appendChild(text);

            // KLIK DI MANA SAJA PADA DIV akan memilih opsi
            div.onclick = (e) => {
                // Cegah jika yang diklik adalah radio (tidak perlu karena radio tersembunyi)
                e.stopPropagation();

                // Update jawaban
                jawaban[q.id] = opt.key;
                simpanJawaban(q.id, opt.key);

                // Update tampilan opsi
                document.querySelectorAll('.option-item').forEach(item => {
                    item.classList.remove('selected');
                });
                div.classList.add('selected');

                // Update radio yang tersembunyi
                radio.checked = true;

                // Update navigasi dan statistik
                updateNavigasi();
                updateStatistik();
            };

            area.appendChild(div);
        });
    } else {
        const textarea = document.createElement('textarea');
        textarea.className = 'essay-area';
        textarea.placeholder = 'Tulis jawaban Anda di sini...';
        textarea.value = jawaban[q.id] || '';
        textarea.oninput = () => {
            jawaban[q.id] = textarea.value;
            simpanJawaban(q.id, textarea.value);
            updateNavigasi();
            updateStatistik();
        };
        area.appendChild(textarea);
    }

    // Update bookmark button
    const btn = document.getElementById('bookmarkBtn');
    if (ditandai[q.id]) {
        btn.classList.add('active');
        btn.textContent = '⭐';
    } else {
        btn.classList.remove('active');
        btn.textContent = '☆';
    }

    updateNavigasiAktif();
    updateTombolNav();
}

function toggleBookmark() {
    const q = soal[indexSekarang];
    if (ditandai[q.id]) delete ditandai[q.id];
    else ditandai[q.id] = true;

    localStorage.setItem(`exam_${EXAM_ID}_bookmarks`, JSON.stringify(ditandai));
    tampilSoal(indexSekarang);
    updateNavigasi();
    updateStatistik();
}

function updateNavigasi() {
    const grid = document.getElementById('navGrid');
    for (let i = 0; i < grid.children.length; i++) {
        const btn = grid.children[i];
        const q = soal[i];
        const terjawab = jawaban[q.id] && jawaban[q.id].trim() !== '';
        const ditandaiQ = ditandai[q.id];

        btn.classList.remove('answered', 'bookmarked');
        if (terjawab) btn.classList.add('answered');
        else if (ditandaiQ) btn.classList.add('bookmarked');
    }
}

function updateNavigasiAktif() {
    const grid = document.getElementById('navGrid');
    for (let i = 0; i < grid.children.length; i++) {
        grid.children[i].classList.remove('active');
        if (i === indexSekarang) {
            grid.children[i].classList.add('active');
        }
    }
}

function updateTombolNav() {
    const prev = document.getElementById('prevBtn');
    const next = document.getElementById('nextBtn');
    prev.disabled = indexSekarang === 0;
    next.disabled = indexSekarang >= soal.length - 1;
}

function updateStatistik() {
    const terjawab = Object.keys(jawaban).filter(id => jawaban[id] && jawaban[id].trim() !== '').length;
    const ditandaiCount = Object.keys(ditandai).length;
    document.getElementById('totalQuestions').textContent = soal.length;
    document.getElementById('answeredCount').textContent = terjawab;
    document.getElementById('unansweredCount').textContent = soal.length - terjawab;
    document.getElementById('bookmarkedCount').textContent = ditandaiCount;
}

function buatNavigasi() {
    const grid = document.getElementById('navGrid');
    grid.innerHTML = '';
    for (let i = 0; i < soal.length; i++) {
        const btn = document.createElement('div');
        btn.className = 'nav-item';
        btn.textContent = i + 1;
        btn.onclick = () => {
            indexSekarang = i;
            tampilSoal(indexSekarang);
        };
        grid.appendChild(btn);
    }
    updateNavigasi();
}

// ==================== INISIALISASI ====================
function mulaiUjian() {
    const forceExit = localStorage.getItem(`exam_${EXAM_ID}_force_exit`);
    if (forceExit === 'true' && !sudahSelesai) {
        localStorage.removeItem(`exam_${EXAM_ID}_answers`);
        localStorage.removeItem(`exam_${EXAM_ID}_bookmarks`);
        localStorage.removeItem(`exam_${EXAM_ID}_start_time`);
        localStorage.removeItem(`exam_${EXAM_ID}_question_order`);
        jawaban = {};
        ditandai = {};
    }
    localStorage.removeItem(`exam_${EXAM_ID}_force_exit`);

    const savedOrder = localStorage.getItem(`exam_${EXAM_ID}_question_order`);
    if (savedOrder && !forceExit) {
        try {
            const order = JSON.parse(savedOrder);
            soal = [...SOAL_ASLI].sort((a,b) => order.indexOf(a.id) - order.indexOf(b.id));
        } catch(e) {
            soal = acakSoal();
        }
    } else {
        soal = acakSoal();
        localStorage.setItem(`exam_${EXAM_ID}_question_order`, JSON.stringify(soal.map(q => q.id)));
    }

    const savedJawaban = localStorage.getItem(`exam_${EXAM_ID}_answers`);
    if (savedJawaban) {
        try { Object.assign(jawaban, JSON.parse(savedJawaban)); } catch(e) {}
    }

    const savedDitandai = localStorage.getItem(`exam_${EXAM_ID}_bookmarks`);
    if (savedDitandai) {
        try { Object.assign(ditandai, JSON.parse(savedDitandai)); } catch(e) {}
    }

    buatNavigasi();
    tampilSoal(0);
    updateStatistik();
    mulaiTimer();
}

// ==================== EVENT LISTENER ====================
document.getElementById('startBtn').onclick = () => {
    masukFullscreen();
    document.getElementById('startScreen').style.display = 'none';
    document.getElementById('examWrapper').style.display = 'block';
    ujianDimulai = true;
    mulaiUjian();
};

document.getElementById('prevBtn').onclick = () => {
    if (indexSekarang > 0) {
        indexSekarang--;
        tampilSoal(indexSekarang);
    }
};

document.getElementById('nextBtn').onclick = () => {
    if (indexSekarang < soal.length - 1) {
        indexSekarang++;
        tampilSoal(indexSekarang);
    }
};

document.getElementById('bookmarkBtn').onclick = toggleBookmark;

document.getElementById('finishBtn').onclick = () => {
    const terjawab = Object.keys(jawaban).filter(id => jawaban[id] && jawaban[id].trim() !== '').length;
    document.getElementById('modalStats').innerHTML = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
            <span>✅ Terjawab</span><span style="font-weight: 700; color: #2d6a4f;">${terjawab}</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span>⏳ Belum dijawab</span><span style="font-weight: 700; color: #dc2626;">${soal.length - terjawab}</span>
        </div>
    `;
    document.getElementById('finishModal').style.display = 'flex';
};

document.getElementById('cancelBtn').onclick = () => {
    document.getElementById('finishModal').style.display = 'none';
};

document.getElementById('confirmBtn').onclick = () => {
    document.getElementById('finishModal').style.display = 'none';
    sedangSubmit = true;
    sudahSelesai = true;
    localStorage.setItem(`exam_${EXAM_ID}_finished`, 'true');
    document.getElementById('submitForm').submit();
};

// ==================== KEAMANAN ====================
for(let i = 0; i < 50; i++) history.pushState(null, null, location.href);
window.onpopstate = (e) => {
    if (ujianDimulai && !sedangSubmit && !sudahSelesai) {
        e.preventDefault();
        toast('🚫 Tombol kembali dinonaktifkan!');
        for(let i = 0; i < 50; i++) history.pushState(null, null, location.href);
    }
};

let startX = 0;
document.ontouchstart = (e) => { startX = e.touches[0].clientX; };
document.ontouchend = (e) => {
    if (ujianDimulai && !sedangSubmit && !sudahSelesai && startX < 50 && e.changedTouches[0].clientX > 100) {
        e.preventDefault();
        toast('🚫 Gesture kembali dinonaktifkan!');
    }
};

window.onbeforeunload = (e) => {
    if (ujianDimulai && !sedangSubmit && !sudahSelesai) {
        e.preventDefault();
        e.returnValue = 'Ujian sedang berlangsung!';
    }
};

document.oncontextmenu = (e) => {
    if (ujianDimulai && !sedangSubmit && !sudahSelesai) {
        e.preventDefault();
        return false;
    }
};

document.addEventListener('visibilitychange', () => {
    if (document.hidden && ujianDimulai && !sedangSubmit && !sudahSelesai) {
        localStorage.setItem(`exam_${EXAM_ID}_force_exit`, 'true');
        toast('⚠️ Jangan tinggalkan ujian!');
    }
});

if (window.Android) {
    window.Android.onBackPressed = () => {
        if (ujianDimulai && !sedangSubmit && !sudahSelesai) {
            toast('🚫 Tombol back dinonaktifkan!');
            return true;
        }
        return false;
    };
}

console.log('✅ Ujian siap - Klik di mana saja pada opsi untuk memilih jawaban');
</script>
</body>
</html>
