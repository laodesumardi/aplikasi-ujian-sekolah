<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Get classes for guru dropdown
        $classes = Kelas::orderBy('level')->orderBy('name')->get();
        return view('teacher.profile', compact('user', 'classes'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'], // Max 2MB
            'remove_avatar' => ['nullable', 'boolean'],
            'guru_kelas' => ['nullable', 'array'], // For guru: array of class IDs
            'guru_kelas.*' => ['nullable', 'exists:classes,id'],
        ];

        // Add password validation if password is provided
        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        // Update basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        // Update kelas (for guru)
        if (!empty($validated['guru_kelas'])) {
            // Store class names as comma-separated string
            $selectedClasses = Kelas::whereIn('id', $validated['guru_kelas'])->pluck('name')->toArray();
            $user->kelas = implode(', ', $selectedClasses);
        } elseif (isset($validated['guru_kelas']) && empty($validated['guru_kelas'])) {
            // If empty array, clear kelas
            $user->kelas = null;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        } elseif (isset($validated['remove_avatar']) && $validated['remove_avatar']) {
            // Remove avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
        }

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('guru.profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
