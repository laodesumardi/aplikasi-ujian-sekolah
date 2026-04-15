<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SubjectsController;
use App\Http\Controllers\Admin\ClassesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\BankSoalController;
use App\Http\Controllers\Guru\UjianController;
use App\Http\Controllers\Guru\HasilController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ExamController as SiswaExamController;
use App\Http\Controllers\Guru\ProfilController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    // Arahkan ke halaman login lokal aplikasi ini
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $role = Auth::user()->role ?? 'siswa';
    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru' => redirect()->route('guru.dashboard'),
        default => redirect()->route('siswa.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Rute Siswa (auth-required)
Route::middleware('auth')->group(function () {
    Route::get('/siswa', [SiswaDashboardController::class, 'index'])->name('siswa.dashboard');
    Route::get('/siswa/ujian-aktif', [SiswaDashboardController::class, 'ujianAktif'])->name('siswa.ujian-aktif');

    Route::get('/siswa/ujian/{exam}', [SiswaExamController::class, 'show'])->name('siswa.exam');
    Route::post('/siswa/ujian/{exam}/save-answer', [SiswaExamController::class, 'saveAnswer'])->name('siswa.exam.save-answer');
    Route::post('/siswa/ujian/{exam}/submit', [SiswaExamController::class, 'submit'])->name('siswa.exam.submit');
    Route::get('/siswa/ujian/{exam}/result', [SiswaExamController::class, 'result'])->name('siswa.exam.result');

    Route::get('/siswa/riwayat', [SiswaDashboardController::class, 'riwayat'])->name('siswa.riwayat');
    Route::get('/siswa/profil', [SiswaDashboardController::class, 'profil'])->name('siswa.profil');
    Route::post('/siswa/profil', [SiswaDashboardController::class, 'updateProfil'])->name('siswa.profil.update');
});

// Rute Guru (auth-required)
Route::middleware('auth')->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', [GuruDashboardController::class, 'index'])->name('dashboard');

    Route::get('/bank-soal', [BankSoalController::class, 'index'])->name('bank');
    Route::get('/bank-soal/template', [BankSoalController::class, 'downloadTemplate'])->name('bank.template');
    Route::get('/bank-soal/template-doc', [BankSoalController::class, 'downloadDocTemplate'])->name('bank.template-doc');
    Route::post('/bank-soal/import', [BankSoalController::class, 'import'])->name('bank.import');
    Route::post('/bank-soal', [BankSoalController::class, 'store'])->name('bank.store');
    Route::put('/bank-soal/{question}', [BankSoalController::class, 'update'])->name('bank.update');
    Route::delete('/bank-soal/{question}', [BankSoalController::class, 'destroy'])->name('bank.destroy');
    Route::delete('/bank-soal/subject/{subject}', [BankSoalController::class, 'deleteAllBySubject'])->name('bank.delete-all-by-subject');
    Route::get('/bank-soal/{subject}', [BankSoalController::class, 'detail'])->name('bank.detail');

    Route::get('/ujian', [UjianController::class, 'index'])->name('exams');
    Route::post('/ujian', [UjianController::class, 'store'])->name('exams.store');
    Route::put('/ujian/{exam}', [UjianController::class, 'update'])->name('exams.update');
    Route::delete('/ujian/{exam}', [UjianController::class, 'destroy'])->name('exams.destroy');
    Route::get('/ujian/questions', [UjianController::class, 'getQuestions'])->name('exams.questions');
    Route::post('/ujian/{exam}/sync-questions', [UjianController::class, 'syncQuestions'])->name('exams.sync-questions');

    Route::get('/hasil', [HasilController::class, 'index'])->name('results');
    Route::get('/hasil/{exam}', [HasilController::class, 'detail'])->name('results.detail');
    Route::get('/hasil/{exam}/export', [HasilController::class, 'export'])->name('results.export');
    Route::delete('/hasil/{result}', [HasilController::class, 'delete'])->name('results.delete');
    Route::delete('/hasil/{exam}/all', [HasilController::class, 'deleteAll'])->name('results.delete-all');

    Route::get('/profil', [ProfilController::class, 'index'])->name('profile');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profile.update');
});

// Rute Admin (auth-required)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Top 10 Kelas management
    Route::post('/top-classes', [\App\Http\Controllers\Admin\TopClassesController::class, 'save'])->name('top-classes.save');

    Route::get('/users', [UsersController::class, 'index'])->name('users');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/import', [UsersController::class, 'import'])->name('users.import');
    Route::get('/users/template', [UsersController::class, 'downloadTemplate'])->name('users.template');
    Route::get('/users/template-doc', [UsersController::class, 'downloadDocTemplate'])->name('users.template-doc');

    Route::get('/subjects', [SubjectsController::class, 'index'])->name('subjects');
    Route::post('/subjects', [SubjectsController::class, 'store'])->name('subjects.store');
    Route::put('/subjects/{subject}', [SubjectsController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectsController::class, 'destroy'])->name('subjects.destroy');

    Route::get('/classes', [ClassesController::class, 'index'])->name('classes');
    Route::post('/classes', [ClassesController::class, 'store'])->name('classes.store');
    Route::put('/classes/{kelas}', [ClassesController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{kelas}', [ClassesController::class, 'destroy'])->name('classes.destroy');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/search', [SearchController::class, 'search'])->name('search');
});


// Route untuk mencatat force exit
Route::post('/siswa/ujian/{examId}/force-exit', function ($examId) {
    // Catat ke database atau log
    Log::warning('Force exit detected', [
        'exam_id' => $examId,
        'user_id' => Auth::id(),
        'timestamp' => now()
    ]);

    // Optional: Simpan ke tabel exam_logs
    // ExamLog::create([...]);

    return response()->json(['status' => 'logged']);
})->name('siswa.exam.force-exit');

// Route untuk reset ujian
Route::post('/siswa/ujian/{examId}/reset', function ($examId) {
    // Hapus jawaban siswa dari database
    \App\Models\ExamAnswer::where('exam_id', $examId)
        ->where('user_id', Auth::id())
        ->delete();

    // Reset exam_result
    \App\Models\ExamResult::where('exam_id', $examId)
        ->where('user_id', Auth::id())
        ->update([
            'answers' => null,
            'score' => null,
            'finished_at' => null
        ]);

    Log::info('Exam reset due to force exit', [
        'exam_id' => $examId,
        'user_id' => Auth::id()
    ]);

    return response()->json(['status' => 'reset']);
})->name('siswa.exam.reset');




// Full reset endpoint
Route::post('/siswa/ujian/{examId}/full-reset', function ($examId) {
    try {
        $userId = Auth::id();

        // Hapus semua jawaban dari database
        DB::table('exam_answers')
            ->where('exam_id', $examId)
            ->where('user_id', $userId)
            ->delete();

        // Reset exam_result
        DB::table('exam_results')
            ->where('exam_id', $examId)
            ->where('user_id', $userId)
            ->update([
                'answers' => null,
                'score' => null,
                'finished_at' => null,
                'started_at' => now() // Reset waktu mulai
            ]);

        Log::info('Full reset executed', [
            'exam_id' => $examId,
            'user_id' => $userId
        ]);

        return response()->json(['status' => 'success', 'message' => 'Exam reset complete']);
    } catch (\Exception $e) {
        Log::error('Full reset failed: ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
})->name('siswa.exam.full-reset');

// Save answer endpoint
Route::post('/siswa/ujian/{examId}/save-answer', function ($examId) {
    try {
        $data = request()->validate([
            'question_id' => 'required|integer',
            'answer' => 'nullable|string'
        ]);

        DB::table('exam_answers')->updateOrInsert(
            [
                'exam_id' => $examId,
                'question_id' => $data['question_id'],
                'user_id' => Auth::id()
            ],
            ['answer' => $data['answer'], 'updated_at' => now()]
        );

        return response()->json(['status' => 'saved']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error'], 500);
    }
})->name('siswa.exam.save-answer');

// Force exit logging
Route::post('/siswa/ujian/{examId}/force-exit', function ($examId) {
    Log::warning('Force exit detected', [
        'exam_id' => $examId,
        'user_id' => Auth::id(),
        'exit_count' => request()->input('exit_count')
    ]);
    return response()->json(['status' => 'logged']);
})->name('siswa.exam.force-exit');
