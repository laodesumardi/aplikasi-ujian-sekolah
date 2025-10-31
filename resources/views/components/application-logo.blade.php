@php
    use App\Models\AppSetting;
    $logoPath = AppSetting::getValue('logo_path', null);
    $logoUrl = $logoPath ? \Storage::url($logoPath) : null;
    
    // Fallback to old logo files if no database logo
    if (!$logoUrl) {
        $candidates = [
            public_path('images/logo.png'),
            public_path('images/logo.jpg'),
            public_path('images/logo.jpeg'),
            public_path('images/logo.svg'),
            public_path('logo.png'),
            public_path('logo.svg'),
        ];
        $logoFile = null;
        foreach ($candidates as $c) {
            if (file_exists($c)) { $logoFile = $c; break; }
        }
        $logoUrl = $logoFile ? asset(str_replace(public_path() . DIRECTORY_SEPARATOR, '', $logoFile)) : null;
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
