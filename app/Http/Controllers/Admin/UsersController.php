<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role') && $request->role !== 'all' && $request->role) {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get classes for guru dropdown
        $classes = Kelas::orderBy('level')->orderBy('name')->get();
        
        return view('admin.users', compact('users', 'classes'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,guru,siswa'],
            'kelas' => ['nullable', 'string', 'max:255'],
        ];
        
        // Add conditional validation based on role
        if ($request->role === 'guru') {
            $rules['guru_kelas'] = ['required', 'array', 'min:1'];
            $rules['guru_kelas.*'] = ['required', 'exists:classes,id'];
        } elseif ($request->role === 'siswa') {
            $rules['siswa_tingkat'] = ['required', 'string', 'in:1,2,3,4,5,6,7,8,9,10,11,12,X,XI,XII'];
            $rules['siswa_sub_kelas'] = ['required', 'string', 'in:A,B,C,D'];
        }
        
        $validated = $request->validate($rules);

        $kelasValue = null;
        
        if ($validated['role'] === 'guru' && !empty($validated['guru_kelas'])) {
            // For guru: store class names as comma-separated string
            $selectedClasses = Kelas::whereIn('id', $validated['guru_kelas'])->pluck('name')->toArray();
            $kelasValue = implode(', ', $selectedClasses);
        } elseif ($validated['role'] === 'siswa' && !empty($validated['siswa_tingkat']) && !empty($validated['siswa_sub_kelas'])) {
            // For siswa: combine tingkat and sub_kelas (e.g., "X A", "XI B")
            $kelasValue = $validated['siswa_tingkat'] . ' ' . $validated['siswa_sub_kelas'];
        } elseif (!empty($validated['kelas'])) {
            // Fallback to manual input
            $kelasValue = $validated['kelas'];
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'kelas' => $kelasValue,
        ]);

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,guru,siswa'],
            'kelas' => ['nullable', 'string', 'max:255'],
        ];
        
        // Add conditional validation based on role
        if ($request->role === 'guru') {
            $rules['guru_kelas'] = ['required', 'array', 'min:1'];
            $rules['guru_kelas.*'] = ['required', 'exists:classes,id'];
        } elseif ($request->role === 'siswa') {
            $rules['siswa_tingkat'] = ['required', 'string', 'in:1,2,3,4,5,6,7,8,9,10,11,12,X,XI,XII'];
            $rules['siswa_sub_kelas'] = ['required', 'string', 'in:A,B,C,D'];
        }
        
        $validated = $request->validate($rules);

        $kelasValue = null;
        
        if ($validated['role'] === 'guru' && !empty($validated['guru_kelas'])) {
            // For guru: store class names as comma-separated string
            $selectedClasses = Kelas::whereIn('id', $validated['guru_kelas'])->pluck('name')->toArray();
            $kelasValue = implode(', ', $selectedClasses);
        } elseif ($validated['role'] === 'siswa' && !empty($validated['siswa_tingkat']) && !empty($validated['siswa_sub_kelas'])) {
            // For siswa: combine tingkat and sub_kelas (e.g., "X A", "XI B")
            $kelasValue = $validated['siswa_tingkat'] . ' ' . $validated['siswa_sub_kelas'];
        } elseif (!empty($validated['kelas'])) {
            // Fallback to manual input
            $kelasValue = $validated['kelas'];
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'kelas' => $kelasValue,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls'],
        ]);

        // TODO: Implement import logic
        return redirect()->route('admin.users')->with('success', 'Import data sedang diproses.');
    }
}



