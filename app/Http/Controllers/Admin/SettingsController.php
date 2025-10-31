<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $appName = AppSetting::getValue('app_name', 'CBT Admin Sekolah');
        $logoPath = AppSetting::getValue('logo_path', null);

        // Normalize legacy storage path to public/uploads for preview without symlink
        if ($logoPath) {
            $normalized = ltrim($logoPath, '/');
            $publicFile = public_path($normalized);

            if (!($publicFile && file_exists($publicFile))) {
                // Handle legacy path with "storage/" prefix
                if (Str::startsWith($normalized, 'storage/')) {
                    $relative = Str::after($normalized, 'storage/');
                    if (Storage::disk('public')->exists($relative)) {
                        $uploadsDir = public_path('uploads');
                        if (!is_dir($uploadsDir)) {
                            @mkdir($uploadsDir, 0755, true);
                        }
                        $basename = basename($relative);
                        $target = $uploadsDir . DIRECTORY_SEPARATOR . $basename;
                        if (!file_exists($target)) {
                            @copy(Storage::disk('public')->path($relative), $target);
                        }
                        $logoPath = 'uploads/' . $basename;
                        AppSetting::setValue('logo_path', $logoPath);
                    }
                } elseif (Storage::disk('public')->exists($normalized)) {
                    // Legacy path stored relative to storage/app/public
                    $uploadsDir = public_path('uploads');
                    if (!is_dir($uploadsDir)) {
                        @mkdir($uploadsDir, 0755, true);
                    }
                    $basename = basename($normalized);
                    $target = $uploadsDir . DIRECTORY_SEPARATOR . $basename;
                    if (!file_exists($target)) {
                        @copy(Storage::disk('public')->path($normalized), $target);
                    }
                    $logoPath = 'uploads/' . $basename;
                    AppSetting::setValue('logo_path', $logoPath);
                }
            }
        }

        // Fallback: use default public/uploads/logo.png if available
        if (!$logoPath) {
            $defaultLogo = 'uploads/logo.png';
            if (file_exists(public_path($defaultLogo))) {
                $logoPath = $defaultLogo;
            }
        } elseif (!file_exists(public_path($logoPath))) {
            // If the configured logo is missing, fall back to default if present
            $defaultLogo = 'uploads/logo.png';
            if (file_exists(public_path($defaultLogo))) {
                $logoPath = $defaultLogo;
            }
        }
        $tahunAjaran = AppSetting::getValue('tahun_ajaran', '2025/2026');
        $sessionTimeout = AppSetting::getValue('session_timeout', '60');
        $maintenance = AppSetting::getValue('maintenance_mode', 'false') === 'true';
        
        return view('admin.settings', compact('appName', 'logoPath', 'tahunAjaran', 'sessionTimeout', 'maintenance'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,svg', 'max:2048'],
            'tahun_ajaran' => ['nullable', 'string', 'max:255'],
            'session_timeout' => ['nullable', 'integer', 'min:1'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        // Update app name
        AppSetting::setValue('app_name', $validated['app_name']);

        // Handle logo upload (store in public/uploads, no symlink)
        if ($request->hasFile('logo')) {
            try {
                // Delete old logo if exists (support legacy storage and new public path)
                $oldLogoPath = AppSetting::getValue('logo_path');
                if ($oldLogoPath) {
                    // Delete from public if exists
                    $publicOld = public_path($oldLogoPath);
                    if ($publicOld && file_exists($publicOld)) {
                        @unlink($publicOld);
                    }
                    // Delete from storage (legacy)
                    if (Storage::disk('public')->exists($oldLogoPath)) {
                        Storage::disk('public')->delete($oldLogoPath);
                    }
                }

                // Ensure uploads directory exists
                $uploadsDir = public_path('uploads');
                if (!is_dir($uploadsDir)) {
                    if (!@mkdir($uploadsDir, 0755, true)) {
                        return redirect()->route('admin.settings')->with('error', 'Gagal membuat folder uploads. Pastikan folder public memiliki permission yang benar.');
                    }
                }

                // Store new logo into public/uploads
                $logoFile = $request->file('logo');
                $extension = strtolower($logoFile->getClientOriginalExtension());
                $logoName = 'logo.' . $extension;
                $destinationPath = $uploadsDir . DIRECTORY_SEPARATOR . $logoName;
                
                // Move file
                if (!$logoFile->move($uploadsDir, $logoName)) {
                    return redirect()->route('admin.settings')->with('error', 'Gagal menyimpan file logo. Pastikan folder uploads memiliki permission write.');
                }

                // Verify file was saved
                if (!file_exists($destinationPath)) {
                    return redirect()->route('admin.settings')->with('error', 'File logo gagal disimpan.');
                }

                $logoPath = 'uploads/' . $logoName;
                AppSetting::setValue('logo_path', $logoPath);
            } catch (\Exception $e) {
                return redirect()->route('admin.settings')->with('error', 'Terjadi kesalahan saat mengunggah logo: ' . $e->getMessage());
            }
        }

        // Update other settings
        if (isset($validated['tahun_ajaran'])) {
            AppSetting::setValue('tahun_ajaran', $validated['tahun_ajaran']);
        }

        if (isset($validated['session_timeout'])) {
            AppSetting::setValue('session_timeout', (string)$validated['session_timeout']);
        }

        if (isset($validated['maintenance_mode'])) {
            AppSetting::setValue('maintenance_mode', $validated['maintenance_mode'] ? 'true' : 'false');
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
