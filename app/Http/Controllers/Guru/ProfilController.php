<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());

        // Normalize legacy avatar path from storage to public/uploads/avatars (no symlink)
        if (!empty($user->avatar)) {
            $publicFile = public_path($user->avatar);
            if (!($publicFile && file_exists($publicFile))) {
                if (Storage::disk('public')->exists($user->avatar)) {
                    $uploadsDir = public_path('uploads/avatars');
                    if (!is_dir($uploadsDir)) {
                        @mkdir($uploadsDir, 0755, true);
                    }
                    $basename = basename($user->avatar);
                    $target = $uploadsDir . DIRECTORY_SEPARATOR . $basename;
                    if (!file_exists($target)) {
                        @copy(Storage::disk('public')->path($user->avatar), $target);
                    }
                    $user->avatar = 'uploads/avatars/' . $basename;
                    $user->save();
                }
            }
        }

        // Get classes for guru dropdown
        $classes = Kelas::orderBy('level')->orderBy('name')->get();
        return view('teacher.profile', compact('user', 'classes'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

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

        // Handle avatar upload (store in public/uploads/avatars, no symlink)
        if ($request->hasFile('avatar')) {
            // Delete old avatar from public if exists
            if (!empty($user->avatar)) {
                $publicOld = public_path($user->avatar);
                if ($publicOld && file_exists($publicOld)) {
                    @unlink($publicOld);
                }
                // Also delete legacy storage file if exists
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
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
                return redirect()->route('guru.profile')->with('error', 'Gagal menyimpan avatar. Pastikan folder uploads memiliki permission write.');
            }
            $user->avatar = 'uploads/avatars/' . $filename;
        } elseif (isset($validated['remove_avatar']) && $validated['remove_avatar']) {
            // Remove avatar
            if (!empty($user->avatar)) {
                $publicOld = public_path($user->avatar);
                if ($publicOld && file_exists($publicOld)) {
                    @unlink($publicOld);
                }
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
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
