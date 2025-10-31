<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php
            use App\Models\AppSetting;
            use Illuminate\Support\Facades\Storage;
            $appName = AppSetting::getValue('app_name', 'CBT Admin Sekolah');
        @endphp
        <title>{{ $appName }} - Guru</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-body min-h-screen text-gray-900">
        <!-- Header -->
        <header class="fixed top-0 right-0 left-0 md:left-64 z-40 bg-primary text-white shadow-lg transition-all duration-300" id="mainHeader">
            <div class="w-full max-w-screen-2xl mx-auto">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-3 sm:px-4 md:px-6 lg:px-8 py-2 sm:py-2.5 md:py-3 lg:py-4 min-h-[56px] sm:min-h-[60px] md:min-h-[64px] gap-2 sm:gap-0">
                    <div class="flex items-center gap-2 sm:gap-3 md:gap-4 flex-1 min-w-0 w-full sm:w-auto">
                        <!-- Mobile: Sidebar Toggle -->
                        <button id="sidebarToggle" aria-label="Buka menu" class="inline-flex items-center justify-center md:hidden rounded-md p-1.5 sm:p-2 text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <!-- Page Title -->
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <h1 class="font-semibold text-sm sm:text-base md:text-lg lg:text-xl truncate">Dashboard Guru</h1>
                        </div>
                        <!-- User Info - Visible on all screens -->
                        @auth
                            @php
                                $user = Auth::user();
                            @endphp
                            <a href="{{ route('guru.profile') }}" class="flex items-center gap-1.5 md:gap-2 px-2 md:px-3 py-1 md:py-1.5 rounded-lg bg-white/5 hover:bg-white/10 ml-auto transition-colors">
                                @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-6 h-6 md:w-7 md:h-7 rounded-full object-cover border-2 border-white/30 flex-shrink-0">
                                @else
                                    <div class="w-6 h-6 md:w-7 md:h-7 rounded-full bg-white/20 flex items-center justify-center border-2 border-white/30 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5 md:w-4 md:h-4 text-white/90">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                                <span class="hidden sm:inline text-xs md:text-sm whitespace-nowrap">
                                    <span class="hidden lg:inline">Nama: </span><strong class="font-semibold">{{ Str::limit($user->name, 15) }}</strong>
                                </span>
                            </a>
                        @endauth
                    </div>
                    <div class="flex items-center gap-1.5 sm:gap-2 md:gap-3 flex-shrink-0 w-full sm:w-auto">
                        @yield('header-right')
                    </div>
                </div>
                <!-- Breadcrumbs -->
                @hasSection('breadcrumbs')
                <div class="bg-white/10 border-t border-white/10">
                    <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-1.5 md:py-2 text-white/90 text-xs sm:text-sm" aria-label="Breadcrumb">
                        @yield('breadcrumbs')
                    </div>
                </div>
                @endif
            </div>
        </header>

        <!-- Overlay for mobile sidebar -->
        <div id="sidebarOverlay" class="fixed inset-0 z-30 bg-black/40 hidden md:hidden"></div>

        <!-- Sidebar -->
        <aside id="teacherSidebar" class="fixed top-0 left-0 bottom-0 z-40 w-64 bg-primary text-white pt-16 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <!-- Brand: Logo + Title -->
            <div class="px-4 py-4 border-b border-white/10">
                @php
                    $appName = AppSetting::getValue('app_name', 'CBT Admin Sekolah');
                    $appNameParts = explode(' ', $appName, 2);
                    $appNameMain = $appNameParts[0] ?? 'CBT';
                    $appNameSub = $appNameParts[1] ?? 'Admin Sekolah';
                @endphp
                <a href="{{ route('guru.dashboard') }}" class="group flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-white/30 rounded-lg px-2 py-2 -m-2 hover:bg-white/5 transition-colors">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white text-primary shadow-lg ring-2 ring-white/20 group-hover:shadow-xl group-hover:ring-white/30 transition-all flex-shrink-0">
                        <x-application-logo class="h-8 w-8 fill-current" />
                    </span>
                    <span class="leading-tight flex-1 min-w-0">
                        <span class="block font-bold text-lg tracking-tight">{{ $appNameMain }}</span>
                        <span class="block text-xs text-white/75 -mt-0.5 font-medium">Guru</span>
                    </span>
                </a>
            </div>
            <nav class="px-4 py-6 space-y-1">
                <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('guru.dashboard') ? 'bg-white/15' : 'hover:bg-white/10' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.592 0L21.75 12M4.5 10.5V21h4.5v-4.5a.75.75 0 01.75-.75H12a.75.75 0 01.75.75V21h4.5V10.5"/></svg>
                    <span>Dashboard Utama</span>
                </a>
                <a href="{{ route('guru.bank') }}" class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('guru.bank') ? 'bg-white/15' : 'hover:bg-white/10' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V5.25z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6M9 12h6M9 15h6"/></svg>
                    <span>Bank Soal</span>
                </a>
                <a href="{{ route('guru.exams') }}" class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('guru.exams') ? 'bg-white/15' : 'hover:bg-white/10' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-4H3v4a2 2 0 002 2z"/></svg>
                    <span>Manajemen Ujian</span>
                </a>
                <a href="{{ route('guru.results') }}" class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('guru.results') ? 'bg-white/15' : 'hover:bg-white/10' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 15l3-3 3 3 4-4"/></svg>
                    <span>Hasil Ujian Kelas</span>
                </a>
                <a href="{{ route('guru.profile') }}" class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('guru.profile') ? 'bg-white/15' : 'hover:bg-white/10' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4 0-7 2-7 4v2h14v-2c0-2-3-4-7-4z"/></svg>
                    <span>Profil</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-9A2.25 2.25 0 002.25 5.25v13.5A2.25 2.25 0 004.5 21h9v-6"/><path stroke-linecap="round" stroke-linejoin="round" d="M18 12l3 3-3 3M21 15H9"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="pt-32 sm:pt-36 md:pt-40 lg:pt-44 transition-all md:ml-64" id="mainContent">
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
                @yield('content')
            </div>
        </main>
        <!-- Sidebar Toggle & Header Height Script -->
        <script>
            (function() {
                // Sidebar Toggle
                const sidebar = document.getElementById('teacherSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                const toggleBtn = document.getElementById('sidebarToggle');

                function openSidebar() {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                }
                function closeSidebar() {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                }
                toggleBtn && toggleBtn.addEventListener('click', openSidebar);
                overlay && overlay.addEventListener('click', closeSidebar);
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') closeSidebar();
                });

                // Dynamic Header Height Adjustment
                function adjustContentPadding() {
                    const header = document.getElementById('mainHeader');
                    const mainContent = document.getElementById('mainContent');
                    if (header && mainContent) {
                        const headerHeight = header.offsetHeight;
                        // Add extra padding to ensure content is not covered
                        const extraPadding = 8; // 8px extra padding
                        mainContent.style.paddingTop = (headerHeight + extraPadding) + 'px';
                    }
                }

                // Adjust on load and resize
                adjustContentPadding();
                window.addEventListener('resize', adjustContentPadding);
                
                // Adjust when breadcrumbs section appears/disappears
                const observer = new MutationObserver(adjustContentPadding);
                const header = document.getElementById('mainHeader');
                if (header) {
                    observer.observe(header, { childList: true, subtree: true, attributes: true });
                }
            })();
        </script>
    </body>
</html>