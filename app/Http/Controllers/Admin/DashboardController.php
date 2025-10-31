<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $countByRole = User::select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $countByKelas = User::select('kelas', DB::raw('COUNT(*) as total'))
            ->whereNotNull('kelas')
            ->groupBy('kelas')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $recentUsers = User::latest()->limit(5)->get(['id','name','email','role','created_at']);

        $activeSessions = DB::table('sessions')->distinct('user_id')->whereNotNull('user_id')->count('user_id');

        $metrics = [
            'totalUsers' => $totalUsers,
            'totalQuestions' => 0,
            'totalCompletedExams' => 0,
        ];

        $stats = [
            'byRole' => $countByRole,
            'byKelas' => $countByKelas,
            'recentUsers' => $recentUsers,
            'activeSessions' => $activeSessions,
        ];

        // Geo placeholder: depends on available columns (e.g., province/city) or IP geolocation
        $geo = [
            'available' => false,
            'data' => [],
        ];

        return view('admin.dashboard', compact('metrics', 'stats', 'geo'));
    }
}





