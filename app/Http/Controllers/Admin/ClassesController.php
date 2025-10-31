<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::query();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%")
                  ->orWhere('program', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $classes = $query->orderBy('level')->orderBy('name')->paginate(10);
        
        return view('admin.classes', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', 'unique:classes,code'],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'level' => ['nullable', 'string', 'max:255'],
            'program' => ['nullable', 'string', 'max:255'],
        ]);

        Kelas::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'capacity' => $validated['capacity'] ?? null,
            'level' => $validated['level'] ?? null,
            'program' => $validated['program'] ?? null,
        ]);

        return redirect()->route('admin.classes')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', 'unique:classes,code,' . $kelas->id],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'level' => ['nullable', 'string', 'max:255'],
            'program' => ['nullable', 'string', 'max:255'],
        ]);

        $kelas->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'capacity' => $validated['capacity'] ?? null,
            'level' => $validated['level'] ?? null,
            'program' => $validated['program'] ?? null,
        ]);

        return redirect()->route('admin.classes')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('admin.classes')->with('success', 'Kelas berhasil dihapus.');
    }
}
