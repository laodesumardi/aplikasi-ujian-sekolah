@php
    use App\Models\AppSetting;

    $logoPath = AppSetting::getValue('logo_path', null);

    // Generate HTTPS-safe URL for logo
    $logoUrl = null;
    if ($logoPath) {
        // First, check if file exists in public folder (new method: public/uploads/logo.png)
        $publicFile = public_path($logoPath);
        if ($publicFile && file_exists($publicFile)) {
            // File exists in public folder
            if (request()->secure() || config('app.env') === 'production') {
                $logoUrl = secure_asset($logoPath);
            } else {
                $logoUrl = asset($logoPath);
            }
        }
    }

    // Fallback to default logo files if no database logo found
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
