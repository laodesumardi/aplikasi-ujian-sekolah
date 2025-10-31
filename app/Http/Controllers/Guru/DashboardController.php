<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current teacher
        $teacher = Auth::user();
        
        // Get questions count - only questions created by this teacher
        $totalQuestions = Question::where('created_by', $teacher->id)->count();
        
        // Get active exams count - only exams created by this teacher
        $activeExams = Exam::where('created_by', $teacher->id)
            ->where('status', 'active')
            ->count();
        
        // Get completed exams count
        $completedExams = Exam::where('created_by', $teacher->id)
            ->where('status', 'completed')
            ->count();
        
        // Get recent activity (placeholder)
        $recentActivity = [];
        
        // Get students count
        $totalStudents = User::where('role', 'siswa')->count();
        
        // Get classes count
        $totalClasses = Kelas::count();
        
        $metrics = [
            'totalQuestions' => $totalQuestions,
            'activeExams' => $activeExams,
            'completedExams' => $completedExams,
            'totalStudents' => $totalStudents,
            'totalClasses' => $totalClasses,
        ];
        
        return view('teacher.dashboard', compact('metrics', 'teacher'));
    }
}
