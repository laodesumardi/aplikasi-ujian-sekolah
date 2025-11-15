<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class TopClassesController extends Controller
{
    /**
     * Save Top 10 Kelas list into AppSetting as JSON
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'entries' => ['required', 'array', 'max:10'],
            'entries.*.kelas' => ['required', 'string', 'max:255'],
            'entries.*.total' => ['required', 'integer', 'min:0'],
        ]);

        $entries = array_map(function($item) {
            return [
                'kelas' => trim($item['kelas']),
                'total' => (int) $item['total'],
            ];
        }, $validated['entries']);

        AppSetting::setValue('top_classes', json_encode($entries));

        return redirect()->route('admin.dashboard')->with('success', 'Top 10 Kelas berhasil disimpan.');
    }
}