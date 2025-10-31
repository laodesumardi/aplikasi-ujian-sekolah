<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Subject;
use App\Exports\ExamResultsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HasilController extends Controller
{
    public function index(Request $request)
    {
        // Get exams created by the logged-in teacher
        $examsQuery = Exam::with('subject', 'classRelation')
            ->where('created_by', auth()->id());

        // Filter by subject
        if ($request->has('subject') && $request->subject !== 'all' && $request->subject) {
            $examsQuery->where('subject_id', $request->subject);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all' && $request->status) {
            $examsQuery->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $examsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($subQuery) use ($search) {
                      $subQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('classRelation', function($classQuery) use ($search) {
                      $classQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        $exams = $examsQuery->get();

        // Calculate statistics for each exam
        $results = [];
        foreach ($exams as $exam) {
            $examResults = ExamResult::where('exam_id', $exam->id)
                ->where('status', 'completed')
                ->get();

            if ($examResults->count() > 0) {
                $scores = $examResults->pluck('score')->toArray();
                $totalPoints = $exam->total_points ?? ($examResults->first()->total_points ?? 0);
                
                $results[] = [
                    'exam' => $exam,
                    'kelas' => $exam->kelas_name ?? '-',
                    'total_students' => $examResults->count(),
                    'rata' => round(array_sum($scores) / count($scores), 2),
                    'tinggi' => max($scores),
                    'rendah' => min($scores),
                    'total_points' => $totalPoints,
                    'passed' => $examResults->where('percentage', '>=', 60)->count(),
                    'failed' => $examResults->where('percentage', '<', 60)->count(),
                ];
            } else {
                // Include exams with no results yet
                $results[] = [
                    'exam' => $exam,
                    'kelas' => $exam->kelas_name ?? '-',
                    'total_students' => 0,
                    'rata' => 0,
                    'tinggi' => 0,
                    'rendah' => 0,
                    'total_points' => $exam->total_points ?? 0,
                    'passed' => 0,
                    'failed' => 0,
                ];
            }
        }

        // Sort by exam date descending
        usort($results, function($a, $b) {
            return $b['exam']->exam_date <=> $a['exam']->exam_date;
        });

        $subjects = Subject::orderBy('name')->get();

        return view('teacher.results', compact('results', 'subjects'));
    }

    public function detail(Exam $exam)
    {
        // Verify the exam belongs to the logged-in teacher
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $results = ExamResult::with('student')
            ->where('exam_id', $exam->id)
            ->where('status', 'completed')
            ->orderBy('score', 'desc')
            ->get();

        // Calculate statistics
        if ($results->count() > 0) {
            $scores = $results->pluck('score')->toArray();
            $percentages = $results->pluck('percentage')->toArray();
            
            $statistics = [
                'total_students' => $results->count(),
                'average_score' => round(array_sum($scores) / count($scores), 2),
                'average_percentage' => round(array_sum($percentages) / count($percentages), 2),
                'highest_score' => max($scores),
                'lowest_score' => min($scores),
                'passed' => $results->where('percentage', '>=', 60)->count(),
                'failed' => $results->where('percentage', '<', 60)->count(),
            ];
        } else {
            $statistics = [
                'total_students' => 0,
                'average_score' => 0,
                'average_percentage' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'passed' => 0,
                'failed' => 0,
            ];
        }

        return view('teacher.results_detail', compact('exam', 'results', 'statistics'));
    }

    public function delete(ExamResult $result)
    {
        // Verify the exam result belongs to an exam created by the logged-in teacher
        if ($result->exam->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $examId = $result->exam_id;
        $result->delete();

        return redirect()->route('guru.results.detail', $examId)
            ->with('success', 'Hasil ujian berhasil dihapus.');
    }

    public function deleteAll(Exam $exam)
    {
        // Verify the exam belongs to the logged-in teacher
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        ExamResult::where('exam_id', $exam->id)->delete();

        return redirect()->route('guru.results.detail', $exam->id)
            ->with('success', 'Semua hasil ujian berhasil dihapus.');
    }

    public function export(Exam $exam)
    {
        // Verify the exam belongs to the logged-in teacher
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $exam->load('subject', 'classRelation');
        $filename = 'Hasil_Ujian_' . str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $exam->title) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new ExamResultsExport($exam), $filename);
    }
}
