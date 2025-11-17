<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Services\DocUserParser;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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
        
        // Tambahkan validasi kondisional berdasarkan peran (store: wajib kirim field terkait)
        if ($request->role === 'guru') {
            $rules['guru_kelas'] = ['required', 'array', 'min:1'];
            $rules['guru_kelas.*'] = ['required', 'exists:classes,id'];
        } elseif ($request->role === 'siswa') {
            $rules['siswa_tingkat'] = ['required', 'string', 'in:I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII,1,2,3,4,5,6,7,8,9,10,11,12'];
            $rules['siswa_sub_kelas'] = ['required', 'string', 'in:A,B,C,D,E,F,G,H'];
        }
        
        $validated = $request->validate($rules);

        $kelasValue = null;
        
        if ($validated['role'] === 'guru' && $request->filled('guru_kelas')) {
            // For guru: store class names as comma-separated string
            $selectedClasses = Kelas::whereIn('id', $request->input('guru_kelas', []))->pluck('name')->toArray();
            $kelasValue = implode(', ', $selectedClasses);
        } elseif ($validated['role'] === 'siswa' && $request->filled('siswa_tingkat') && $request->filled('siswa_sub_kelas')) {
            // For siswa: normalize tingkat to Roman and combine (e.g., "X A", "XI B")
            $romans = [
                '1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV', '5' => 'V', '6' => 'VI',
                '7' => 'VII', '8' => 'VIII', '9' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
            ];
            $tingkat = $request->input('siswa_tingkat');
            $tingkatRoman = $romans[$tingkat] ?? strtoupper($tingkat);
            $kelasValue = $tingkatRoman . ' ' . $request->input('siswa_sub_kelas');
        } elseif ($request->filled('kelas')) {
            // Fallback to manual input
            $kelasValue = $request->input('kelas');
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
            $rules['siswa_tingkat'] = ['required', 'string', 'in:I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII,1,2,3,4,5,6,7,8,9,10,11,12'];
            $rules['siswa_sub_kelas'] = ['required', 'string', 'in:A,B,C,D,E,F,G,H'];
        }
        
        $validated = $request->validate($rules);

        $kelasValue = null;
        
        if ($validated['role'] === 'guru' && !empty($validated['guru_kelas'])) {
            // For guru: store class names as comma-separated string
            $selectedClasses = Kelas::whereIn('id', $validated['guru_kelas'])->pluck('name')->toArray();
            $kelasValue = implode(', ', $selectedClasses);
        } elseif ($validated['role'] === 'siswa' && !empty($validated['siswa_tingkat']) && !empty($validated['siswa_sub_kelas'])) {
            // For siswa: normalize tingkat to Roman and combine (e.g., "X A", "XI B")
            $romans = [
                '1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV', '5' => 'V', '6' => 'VI',
                '7' => 'VII', '8' => 'VIII', '9' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
            ];
            $tingkat = $validated['siswa_tingkat'];
            $tingkatRoman = $romans[$tingkat] ?? strtoupper($tingkat);
            $kelasValue = $tingkatRoman . ' ' . $validated['siswa_sub_kelas'];
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
            'file' => ['required', 'file'],
        ], [
            'file.required' => 'Silakan pilih file untuk diimpor.',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $allowedExt = ['csv','xlsx','xls','docx'];
        if (!in_array($ext, $allowedExt, true)) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['file' => 'File harus bertipe: CSV, XLSX, XLS, atau DOCX.'])
                ->withInput();
        }

        if (in_array($ext, ['csv','xlsx','xls'])) {
            $import = new UsersImport();
            Excel::import($import, $file);
            $report = $import->report();
            $msg = "Impor selesai. Ditambah: {$report['created']}, diperbarui: {$report['updated']}, dilewati: {$report['skipped']}";
            if (!empty($report['errors'])) {
                $msg .= ". Error: " . implode(' | ', $report['errors']);
            }
            return redirect()->route('admin.users')->with('success', $msg);
        }

        if ($ext === 'docx') {
            $parser = new DocUserParser();
            $rows = $parser->parse($file->getPathname());

            // If parser returns no rows, provide a helpful error
            if (empty($rows)) {
                return redirect()->route('admin.users')->with('error', 'Dokumen DOCX tidak berisi data yang dapat diimpor. Pastikan ada tabel dengan baris pertama sebagai header (name, email, role, kelas, password) dan minimal satu baris data. Anda dapat menggunakan tombol "Template DOCX" sebagai acuan.');
            }

            $created = 0; $updated = 0; $skipped = 0; $errors = [];
            foreach ($rows as $i => $row) {
                $name = trim((string)($row['name'] ?? $row['nama'] ?? ''));
                $email = strtolower(trim((string)($row['email'] ?? '')));
                $role = strtolower(trim((string)($row['role'] ?? $row['peran'] ?? '')));
                $kelas = trim((string)($row['kelas'] ?? ''));
                $password = (string)($row['password'] ?? '');

                if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($role, ['admin','guru','siswa'])) {
                    $skipped++;
                    $errors[] = "Baris DOCX #" . ($i + 1) . " tidak valid.";
                    continue;
                }

                $existing = User::where('email', $email)->first();
                if ($existing) {
                    $data = [
                        'name' => $name,
                        'role' => $role,
                        'kelas' => $kelas ?: null,
                    ];
                    if (!empty($password)) {
                        $data['password'] = Hash::make($password);
                    }
                    $existing->update($data);
                    $updated++;
                } else {
                    $pwd = !empty($password) ? $password : Str::random(10);
                    User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($pwd),
                        'role' => $role,
                        'kelas' => $kelas ?: null,
                    ]);
                    $created++;
                }
            }

            $msg = "Impor DOCX selesai. Ditambah: {$created}, diperbarui: {$updated}, dilewati: {$skipped}";
            if (!empty($errors)) {
                $msg .= ". Error: " . implode(' | ', $errors);
            }
            return redirect()->route('admin.users')->with('success', $msg);
        }

        return redirect()->route('admin.users')->with('error', 'Format file tidak didukung.');
    }

    public function downloadTemplate()
    {
        $headersRow = ['name', 'email', 'role', 'kelas', 'password'];
        $exampleRow = ['John Doe', 'john@example.com', 'siswa', 'X IPA 1', 'password123'];

        $filename = 'template_import_pengguna_' . date('Y-m-d') . '.csv';

        $callback = function() use ($headersRow, $exampleRow) {
            $f = fopen('php://output', 'w');
            // Optional: add UTF-8 BOM for Excel compatibility
            // fwrite($f, "\xEF\xBB\xBF");
            fputcsv($f, $headersRow);
            fputcsv($f, $exampleRow);
            fclose($f);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    public function downloadDocTemplate()
    {
        $phpWord = new PhpWord();

        // Set document properties
        $properties = $phpWord->getDocInfo();
        $appName = \App\Models\AppSetting::getValue('app_name', 'Aplikasi Ujian Sekolah');
        $properties->setCreator($appName);
        $properties->setTitle('Template Import Pengguna - Format DOCX');
        $properties->setDescription('Template untuk impor data pengguna dalam format Microsoft Word (DOCX)');

        // Add section with standard margins
        $section = $phpWord->addSection([
            'marginTop' => 1440,
            'marginBottom' => 1440,
            'marginLeft' => 1440,
            'marginRight' => 1440,
        ]);

        // Title
        $section->addText('TEMPLATE IMPORT PENGGUNA - FORMAT DOCX', [
            'bold' => true,
            'size' => 14,
        ], [
            'alignment' => 'center',
            'spaceAfter' => 240,
        ]);

        // Instructions
        $section->addText('Petunjuk:', [
            'bold' => true,
            'size' => 12,
        ], [
            'spaceBefore' => 240,
            'spaceAfter' => 120,
        ]);

        $instructions = [
            '1. Gunakan tabel di bawah dengan baris pertama sebagai header.',
            '2. Header yang dikenali: name, email, role, kelas, password.',
            '3. Nilai role: admin, guru, atau siswa.',
            '4. Kolom kelas opsional. Untuk guru dapat berisi beberapa nama kelas dipisahkan koma.',
            '5. Kolom password opsional. Jika kosong, sistem akan membuatkan otomatis.',
        ];
        foreach ($instructions as $line) {
            $section->addText($line, ['size' => 10]);
        }

        // Table styles
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '999999',
            'cellMargin' => 80,
        ];
        $firstRowStyle = [
            'bgColor' => 'DDDDDD',
        ];
        $phpWord->addTableStyle('UsersImportTable', $tableStyle, $firstRowStyle);

        // Add table with headers and one example row
        $table = $section->addTable('UsersImportTable');
        $headers = ['name', 'email', 'role', 'kelas', 'password'];
        $example = ['John Doe', 'john@example.com', 'siswa', 'X IPA 1', 'password123'];

        $headerRow = $table->addRow();
        foreach ($headers as $h) {
            $cell = $headerRow->addCell(3000);
            $cell->addText($h, ['bold' => true]);
        }

        $dataRow = $table->addRow();
        foreach ($example as $val) {
            $cell = $dataRow->addCell(3000);
            $cell->addText($val);
        }

        $filename = 'template_import_pengguna_' . date('Y-m-d') . '.docx';

        // Save to temporary file and stream download
        $tempFile = tempnam(sys_get_temp_dir(), 'template_users_');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}



