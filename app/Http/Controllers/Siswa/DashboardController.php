<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Get available exams for student's class
        $availableExamsQuery = Exam::with('subject', 'classRelation')
            ->where('status', 'active');

        // Filter by class - if student has kelas, filter by it. Otherwise show all active exams
        if ($user->kelas) {
            $availableExamsQuery->where(function($query) use ($user) {
                // Match by kelas field (exact match)
                $query->where('kelas', $user->kelas)
                      // Or match by class_id relation
                      ->orWhereHas('classRelation', function($q) use ($user) {
                          $q->where('name', $user->kelas);
                      })
                      // Partial match for kelas field
                      ->orWhere('kelas', 'like', $user->kelas . '%')
                      ->orWhere('kelas', 'like', '%' . $user->kelas . '%');
            });
        }

        // Filter by date - show exams that are available now or already started
        $availableExamsQuery->where(function($query) use ($now) {
            // Exam with no date restriction (always available)
            $query->whereNull('exam_date')
                  // Or exam date is today or in the past
                  ->orWhere('exam_date', '<=', $now->toDateString());
        });

        // Additional check for time - if exam date is today, check if start time has passed
        // But if exam date is in the past, show it regardless of time
        $availableExamsQuery->where(function($query) use ($now) {
            $query->whereNull('exam_date')
                  ->orWhereNull('start_time')
                  ->orWhere('exam_date', '<', $now->toDateString())
                  ->orWhere(function($q) use ($now) {
                      $q->where('exam_date', $now->toDateString())
                        ->where(function($timeQuery) use ($now) {
                            $timeQuery->whereNull('start_time')
                                     ->orWhere('start_time', '<=', $now->toTimeString());
                        });
                  });
        });

        $availableExams = $availableExamsQuery
            ->orderBy('exam_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        // Get scheduled exams (upcoming)
        $scheduledExams = Exam::with('subject', 'classRelation')
            ->where(function($query) use ($user) {
                $query->where('kelas', $user->kelas)
                      ->orWhereHas('classRelation', function($q) use ($user) {
                          if ($user->kelas) {
                              $q->where('name', $user->kelas);
                          }
                      });
            })
            ->whereIn('status', ['scheduled', 'draft'])
            ->where(function($query) use ($now) {
                $query->where('exam_date', '>', $now->toDateString())
                      ->orWhere(function($q) use ($now) {
                          $q->where('exam_date', $now->toDateString())
                            ->where('start_time', '>', $now->toTimeString());
                      });
            })
            ->orderBy('exam_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        // Get exam results (completed exams)
        $examResults = ExamResult::with('exam.subject')
            ->where('student_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate statistics
        $totalExams = ExamResult::where('student_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $averageScore = ExamResult::where('student_id', $user->id)
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;

        $highestScore = ExamResult::where('student_id', $user->id)
            ->where('status', 'completed')
            ->max('percentage') ?? 0;

        $metrics = [
            'totalExams' => $totalExams,
            'averageScore' => round($averageScore, 2),
            'highestScore' => round($highestScore, 2),
            'availableExamsCount' => $availableExams->count(),
            'scheduledExamsCount' => $scheduledExams->count(),
        ];

        return view('student.dashboard', compact('availableExams', 'scheduledExams', 'examResults', 'metrics', 'user'));
    }

    public function ujianAktif(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Get available exams for student's class
        $query = Exam::with('subject', 'classRelation')
            ->where('status', 'active');

        // Filter by class - if student has kelas, filter by it. Otherwise show all active exams
        if ($user->kelas && trim($user->kelas) !== '') {
            $studentKelas = trim($user->kelas);
            $query->where(function($q) use ($studentKelas) {
                // Match by kelas field (exact match - case insensitive, with trimmed spaces)
                $q->whereRaw('TRIM(LOWER(kelas)) = ?', [strtolower($studentKelas)])
                  // Or match by class_id relation
                  ->orWhereHas('classRelation', function($subQuery) use ($studentKelas) {
                      $subQuery->whereRaw('TRIM(LOWER(name)) = ?', [strtolower($studentKelas)]);
                  })
                  // Partial match for kelas field (starts with - case insensitive)
                  ->orWhereRaw('LOWER(kelas) LIKE ?', [strtolower($studentKelas) . '%'])
                  // Partial match for kelas field (ends with - case insensitive)
                  ->orWhereRaw('LOWER(kelas) LIKE ?', ['%' . strtolower($studentKelas)])
                  // Partial match for kelas field (contains - case insensitive)
                  ->orWhereRaw('LOWER(kelas) LIKE ?', ['%' . strtolower($studentKelas) . '%'])
                  // Or if exam has no kelas restriction (show all active exams regardless of kelas)
                  ->orWhereNull('kelas')
                  ->orWhere('kelas', '');
            });
        }
        // If student doesn't have kelas, show all active exams

        // Filter by date and time - show exams that are available now
        // Since status is already 'active', if teacher set it to active, it should be accessible
        // We'll be more lenient - show active exams if:
        // 1. No date restriction (always available)
        // 2. Exam date is today or in the past (always accessible)
        // 3. Exam date is today - if start_time exists, check if it has passed; if no start_time, always show
        // 4. Exam date is in the future - still show if status is active (teacher may have set it in advance)
        
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();
        
        // For active exams, we'll show them regardless of date if status is already active
        // But we'll still check for basic date/time logic for exams happening today
        $query->where(function($q) use ($today, $currentTime) {
            // Exam with no date restriction (always available)
            $q->whereNull('exam_date')
              // Or exam date is in the past (always accessible if status is active)
              ->orWhere('exam_date', '<', $today)
              // Or exam date is today or in the future - show active exams regardless
              // This allows teachers to set exams as active in advance
              ->orWhere('exam_date', '>=', $today);
        });

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($subQuery) use ($search) {
                      $subQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Subject filter
        if ($request->has('subject') && $request->subject !== 'all' && $request->subject) {
            $query->where('subject_id', $request->subject);
        }

        $availableExams = $query->orderBy('exam_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(12);

        // Get all subjects for filter
        $subjects = \App\Models\Subject::orderBy('name')->get();

        return view('student.ujian_aktif', compact('availableExams', 'subjects'));
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();

        // Get exam results for the student
        $query = ExamResult::with('exam.subject')
            ->where('student_id', $user->id)
            ->where('status', 'completed');

        // Filter by subject
        if ($request->has('subject') && $request->subject !== 'all' && $request->subject) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->where('subject_id', $request->subject);
            });
        }

        // Filter by status (passed/failed)
        if ($request->has('status') && $request->status !== 'all' && $request->status) {
            if ($request->status === 'passed') {
                $query->where('percentage', '>=', 60);
            } elseif ($request->status === 'failed') {
                $query->where('percentage', '<', 60);
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('exam', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($subQuery) use ($search) {
                      $subQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'submitted_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['submitted_at', 'score', 'percentage'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('submitted_at', 'desc');
        }

        $results = $query->paginate(15);

        // Get all subjects for filter
        $subjects = \App\Models\Subject::orderBy('name')->get();

        // Calculate statistics
        $totalExams = ExamResult::where('student_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $averageScore = ExamResult::where('student_id', $user->id)
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;

        $highestScore = ExamResult::where('student_id', $user->id)
            ->where('status', 'completed')
            ->max('percentage') ?? 0;

        $passedCount = ExamResult::where('student_id', $user->id)
            ->where('status', 'completed')
            ->where('percentage', '>=', 60)
            ->count();

        $failedCount = ExamResult::where('student_id', $user->id)
            ->where('status', 'completed')
            ->where('percentage', '<', 60)
            ->count();

        $statistics = [
            'totalExams' => $totalExams,
            'averageScore' => round($averageScore, 2),
            'highestScore' => round($highestScore, 2),
            'passedCount' => $passedCount,
            'failedCount' => $failedCount,
        ];

        return view('student.history', compact('results', 'subjects', 'statistics'));
    }

    public function profil()
    {
        $user = Auth::user();

        // Normalize legacy avatar path from storage to public/uploads/avatars (no symlink)
        if (!empty($user->avatar)) {
            $publicFile = public_path($user->avatar);
            if (!($publicFile && file_exists($publicFile))) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    $uploadsDir = public_path('uploads/avatars');
                    if (!is_dir($uploadsDir)) {
                        @mkdir($uploadsDir, 0755, true);
                    }
                    $basename = basename($user->avatar);
                    $target = $uploadsDir . DIRECTORY_SEPARATOR . $basename;
                    if (!file_exists($target)) {
                        @copy(\Illuminate\Support\Facades\Storage::disk('public')->path($user->avatar), $target);
                    }
                    $user->avatar = 'uploads/avatars/' . $basename;
                    $user->save();
                }
            }
        }

        $classes = \App\Models\Kelas::orderBy('level')->orderBy('name')->get();
        return view('student.profile', compact('user', 'classes'));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'kelas' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'], // Max 2MB
            'remove_avatar' => ['nullable', 'boolean'],
        ];

        // Add password validation if password is provided
        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()];
        }

        $validated = $request->validate($rules);

        // Update basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->kelas = $validated['kelas'] ?? null;

        // Handle avatar upload (store in public/uploads/avatars, no symlink)
        if ($request->hasFile('avatar')) {
            // Delete old avatar from public if exists
            if (!empty($user->avatar)) {
                $publicOld = public_path($user->avatar);
                if ($publicOld && file_exists($publicOld)) {
                    @unlink($publicOld);
                }
                // Also delete legacy storage file if exists
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
            }

            // Ensure uploads directory exists
            $uploadsDir = public_path('uploads/avatars');
            if (!is_dir($uploadsDir)) {
                @mkdir($uploadsDir, 0755, true);
            }

            // Move new avatar to public/uploads/avatars
            $file = $request->file('avatar');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;
            if (!$file->move($uploadsDir, $filename)) {
                return redirect()->route('siswa.profil')->with('error', 'Gagal menyimpan avatar. Pastikan folder uploads memiliki permission write.');
            }
            $user->avatar = 'uploads/avatars/' . $filename;
        } elseif (isset($validated['remove_avatar']) && $validated['remove_avatar']) {
            // Remove avatar
            if (!empty($user->avatar)) {
                $publicOld = public_path($user->avatar);
                if ($publicOld && file_exists($publicOld)) {
                    @unlink($publicOld);
                }
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
            }
            $user->avatar = null;
        }

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('siswa.profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
