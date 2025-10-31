@php
    use App\Models\AppSetting;
    use Illuminate\Support\Facades\Storage;

    $logoPath = AppSetting::getValue('logo_path', null);

    // Generate HTTPS-safe URL for storage-based logo
    $logoUrl = null;
    if ($logoPath) {
        // Check if file exists in storage
        if (Storage::disk('public')->exists($logoPath)) {
            // Use Storage::url to get the correct public URL (e.g., '/storage/images/logo.png')
            $storageUrl = Storage::url($logoPath);
            $publicPath = ltrim($storageUrl, '/');
            
            // Use secure_asset for HTTPS or asset for HTTP
            // This ensures proper URL generation based on APP_URL in .env
            if (request()->secure() || config('app.env') === 'production') {
                $logoUrl = secure_asset($publicPath);
            } else {
                $logoUrl = asset($publicPath);
            }
        }
    }

    // Fallback to public files if no database logo
    if (!$logoUrl) {
        $candidates = [
            'images/logo.png',
            'images/logo.jpg',
            'images/logo.jpeg',
            'images/logo.svg',
            'logo.png',
            'logo.svg',
        ];
        $logoFile = null;
        foreach ($candidates as $c) {
            $fullPath = public_path($c);
            if (file_exists($fullPath)) { 
                $logoFile = $c; 
                break; 
            }
        }
        // Use secure_asset for HTTPS or asset for HTTP
        if ($logoFile) {
            if (request()->secure() || config('app.env') === 'production') {
                $logoUrl = secure_asset($logoFile);
            } else {
                $logoUrl = asset($logoFile);
            }
        }
    }
@endphp

@if ($logoUrl)
    <img src="{{ $logoUrl }}" alt="Logo" {{ $attributes }} />
@else
<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" {{ $attributes }}>
    <!-- Ikon placeholder: kartu ujian dengan centang -->
    <rect x="4" y="5" width="16" height="14" rx="2" ry="2" fill="none" stroke="currentColor" stroke-width="1.5" />
    <path d="M8 12l2.5 2.5L16 9.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
    <path d="M6 3h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
    <path d="M6 21h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
</svg>
@endif
