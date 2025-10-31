<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Kelas;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->route('admin.dashboard');
        }

        $results = [
            'users' => collect(),
            'subjects' => collect(),
            'classes' => collect(),
        ];

        // Search users
        $users = User::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('kelas', 'like', "%{$query}%");
        })->limit(5)->get();

        // Search subjects
        $subjects = Subject::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('code', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })->limit(5)->get();

        // Search classes
        $classes = Kelas::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('code', 'like', "%{$query}%")
              ->orWhere('level', 'like', "%{$query}%")
              ->orWhere('program', 'like', "%{$query}%");
        })->limit(5)->get();

        $results['users'] = $users;
        $results['subjects'] = $subjects;
        $results['classes'] = $classes;

        return view('admin.search', compact('query', 'results'));
    }
}
