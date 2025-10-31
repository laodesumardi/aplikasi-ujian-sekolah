@extends('layouts.admin')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:underline">Admin</a>
        <span>/</span>
        <span class="text-white">Dashboard</span>
    </nav>
@endsection

@section('header-right')
    <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927a1 1 0 011.902 0l.26.894a1 1 0 00.76.69l.917.203a1 1 0 01.516 1.676l-.654.757a1 1 0 00-.23.746l.1.934a1 1 0 01-1.003 1.102h-.929a1 1 0 00-.796.39l-.602.77a1 1 0 01-1.648 0l-.602-.77a1 1 0 00-.796-.39h-.929a1 1 0 01-1.003-1.102l.1-.934a1 1 0 00-.23-.746l-.654-.757a1 1 0 01.516-1.676l.917-.203a1 1 0 00.76-.69l.26-.894z"/></svg>
        <span>Pengaturan</span>
    </a>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Welcome Section -->
        <div class="mb-6 lg:mb-8">
            <div class="bg-gradient-to-r from-primary via-primary/95 to-primary/90 p-6 md:p-8 lg:p-10 text-white">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 lg:gap-6">
                    @php
                        use App\Models\AppSetting;
                        $appName = AppSetting::getValue('app_name', 'CBT Admin Sekolah');
                    @endphp
                    <div class="flex-1">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-2 lg:mb-3 leading-tight">Selamat Datang, {{ Auth::user()->name }}!</h1>
                        <p class="text-base md:text-lg lg:text-xl text-white/90 leading-relaxed">Berikut adalah ringkasan statistik dan aktivitas sistem {{ $appName }}.</p>
                    </div>
                    <div class="hidden xl:block flex-shrink-0">
                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-10 h-10 lg:w-12 lg:h-12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrics Cards -->
        <section aria-labelledby="admin-metrics" class="mb-6 lg:mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                <!-- Total Pengguna -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Total Pengguna</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['totalUsers']) }}</p>
                            <p class="text-xs text-gray-600 mt-2">Semua pengguna terdaftar</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 110-8 4 4 0 010 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            <!-- Total Soal -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Total Soal</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['totalQuestions']) }}</p>
                            <p class="text-xs text-gray-600 mt-2">Soal tersedia di bank soal</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h18M5 9h14M7 13h10M9 17h6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Ujian Selesai -->
                <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group sm:col-span-2 lg:col-span-1">
                    <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-primary font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Ujian Selesai</p>
                            <p class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-1 leading-none">{{ number_format($metrics['totalCompletedExams']) }}</p>
                            <p class="text-xs text-gray-600 mt-2">Total ujian yang telah diselesaikan</p>
                        </div>
                        <div class="bg-primary rounded-lg lg:rounded-xl p-3 md:p-4 shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-8 h-8 md:w-10 md:h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 15l3-3 3 3 4-4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Grid -->
        <section class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6 mb-6 lg:mb-8" aria-labelledby="admin-stats">
            <!-- Distribusi Peran -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl transition-shadow">
                <h2 class="text-base md:text-lg font-bold text-gray-900 mb-4 md:mb-5 flex items-center gap-2">
                    <div class="bg-primary/10 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 md:w-5 md:h-5 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span>Distribusi Pengguna</span>
                </h2>
                <div class="space-y-3 md:space-y-4">
                    @php 
                        $roles = ['admin' => ['label' => 'Admin', 'color' => 'bg-red-500'], 'guru' => ['label' => 'Guru', 'color' => 'bg-primary'], 'siswa' => ['label' => 'Siswa', 'color' => 'bg-green-500']];
                        $totalRoleUsers = ($stats['byRole']['admin'] ?? 0) + ($stats['byRole']['guru'] ?? 0) + ($stats['byRole']['siswa'] ?? 0);
                    @endphp
                    @foreach ($roles as $key => $roleInfo)
                        @php
                            $count = $stats['byRole'][$key] ?? 0;
                            $percentage = $totalRoleUsers > 0 ? ($count / $totalRoleUsers) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-3 h-3 rounded-full {{ $roleInfo['color'] }}"></span>
                                    <span class="text-sm font-medium text-gray-700">{{ $roleInfo['label'] }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($count) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 md:h-2.5">
                                <div class="{{ $roleInfo['color'] }} h-2 md:h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 md:mt-1.5">{{ number_format($percentage, 1) }}% dari total</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Kelas -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl transition-shadow">
                <h2 class="text-base md:text-lg font-bold text-gray-900 mb-4 md:mb-5 flex items-center gap-2">
                    <div class="bg-primary/10 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 md:w-5 md:h-5 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h8"/>
                        </svg>
                    </div>
                    <span>Top 10 Kelas</span>
                </h2>
                <div class="overflow-x-auto -mx-5 md:-mx-6 px-5 md:px-6">
                    <table class="min-w-full text-xs md:text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-3 md:px-4 py-2 md:py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                                <th class="text-left px-3 md:px-4 py-2 md:py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Kelas</th>
                                <th class="text-right px-3 md:px-4 py-2 md:py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($stats['byKelas'] as $index => $row)
                                <tr class="hover:bg-primary/5 transition-colors">
                                    <td class="px-3 md:px-4 py-2 md:py-3 text-gray-500 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-3 md:px-4 py-2 md:py-3 font-semibold text-gray-900">{{ $row->kelas ?? '-' }}</td>
                                    <td class="px-3 md:px-4 py-2 md:py-3 text-right font-bold text-primary text-base md:text-lg">{{ number_format($row->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 md:px-4 py-4 md:py-6 text-gray-500 text-center" colspan="3">Belum ada data kelas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aktivitas & Pengguna Baru -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl transition-shadow lg:col-span-2 xl:col-span-1">
                <h2 class="text-base md:text-lg font-bold text-gray-900 mb-4 md:mb-5 flex items-center gap-2">
                    <div class="bg-primary/10 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 md:w-5 md:h-5 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span>Aktivitas Terkini</span>
                </h2>
                
                <div class="mb-4 md:mb-6 p-4 md:p-5 bg-gradient-to-r from-primary/10 to-primary/5 rounded-lg md:rounded-xl">
                    <div class="flex items-center justify-between">
                    <div>
                            <p class="text-xs text-primary font-semibold mb-1 uppercase tracking-wide">Sesi Aktif</p>
                            <p class="text-2xl md:text-3xl font-bold text-primary">{{ number_format($stats['activeSessions']) }}</p>
                        </div>
                        <div class="bg-primary rounded-full p-2 md:p-3 shadow-lg flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-5 h-5 md:w-6 md:h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <h3 class="text-xs md:text-sm font-bold text-gray-900 mb-3 md:mb-4">Pengguna Terbaru</h3>
                <ul class="space-y-2 md:space-y-3">
                    @forelse ($stats['recentUsers'] as $u)
                        <li class="flex items-start justify-between p-2 md:p-3 bg-gray-50 rounded-lg md:rounded-xl hover:bg-primary/5 transition-colors">
                            <div class="flex-1 min-w-0 pr-2">
                                <p class="font-semibold text-gray-900 truncate mb-1 text-sm">{{ $u->name }}</p>
                                <p class="text-xs text-gray-600 truncate mb-2">{{ $u->email }}</p>
                                <span class="inline-block px-2 md:px-2.5 py-1 text-xs rounded-full bg-primary/10 text-primary font-medium">{{ ucfirst($u->role) }}</span>
                            </div>
                            <span class="text-xs text-gray-500 whitespace-nowrap mt-1 flex-shrink-0">{{ $u->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500 text-center py-4">Belum ada data.</li>
                    @endforelse
                </ul>
            </div>
        </section>

        <!-- Geografis Section -->
        @if (!empty($geo['available']))
        <section class="mb-6 lg:mb-8" aria-labelledby="admin-geo">
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg p-5 md:p-6 hover:shadow-xl transition-shadow">
                <h2 id="admin-geo" class="text-base md:text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="bg-primary/10 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 md:w-5 md:h-5 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span>Sebaran Geografis</span>
                </h2>
                <div id="map" class="w-full h-64 md:h-80 bg-body rounded-lg md:rounded-xl"></div>
        </div>
    </section>
        @endif
    </div>
@endsection