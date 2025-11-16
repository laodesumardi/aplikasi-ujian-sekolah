@php
    use App\Models\AppSetting;

    $logoPath = AppSetting::getValue('logo_path', null);

    // Generate URL for logo (prefer relative paths to avoid host/port mismatch)
    $logoUrl = null;
    if ($logoPath) {
        // Normalize path
        $normalizedPath = ltrim($logoPath, '/');
        $publicFile = public_path($normalizedPath);
        
        // Check if file exists in public folder
        if ($publicFile && file_exists($publicFile)) {
            // Build relative URL to current host
            $relativePath = '/' . ltrim($normalizedPath, '/');
            $logoUrl = $relativePath . '?v=' . filemtime($publicFile);
        }
    }

    // Fallback to default logo files if no database logo found
    if (!$logoUrl) {
        $candidates = [
            'uploads/logo.png',
            'uploads/logo.svg',
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
        // Build relative URL to current host (with cache busting)
        if ($logoFile) {
            $relativePath = '/' . ltrim($logoFile, '/');
            $fullPath = public_path($logoFile);
            $version = file_exists($fullPath) ? ('?v=' . filemtime($fullPath)) : '';
            $logoUrl = $relativePath . $version;
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
