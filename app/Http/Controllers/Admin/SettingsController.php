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
                @mkdir($uploadsDir, 0755, true);
            }

            // Store new logo into public/uploads
            $logoFile = $request->file('logo');
            $extension = $logoFile->getClientOriginalExtension();
            $logoName = 'logo.' . $extension;
            $logoFile->move($uploadsDir, $logoName);

            $logoPath = 'uploads/' . $logoName;
            AppSetting::setValue('logo_path', $logoPath);
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
