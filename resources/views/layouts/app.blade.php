<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ASISKU')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F4F5F7;
        }
    </style>
</head>
<body class="min-h-screen flex text-slate-800">

    @php
        $role = auth()->user()->role;
        $roleTitle = match($role) {
            'asprak'      => 'ASISKU - Asisten Praktikum',
            'dosen'       => 'ASISKU - Dosen',
            'koor_asprak' => 'ASISKU - Koor Asisten',
            default       => 'ASISKU',
        };
        $badgeCount = $badgeCount ?? 0;
    @endphp

    <!-- SIDEBAR KIRI -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0">
        <div>
            <!-- Logo ASISKU -->
            <div class="pt-7 pb-5 flex justify-center">
                <img src="{{ asset('images/logo-asisku.png') }}" alt="Logo ASISKU" class="h-20 object-contain">
            </div>

            <!-- Label Role -->
            <div class="px-6 py-3">
                <p class="text-sm font-extrabold text-[#08244B]">{{ $roleTitle }}</p>
            </div>

            <!-- Menu Navigasi Berdasarkan Role -->
            <nav class="mt-2 space-y-1">
                @if($role === 'asprak')
                    <a href="{{ route('asprak.dashboard') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('asprak.dashboard') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>DASHBOARD</span>
                    </a>
                    <a href="{{ route('asprak.jadwal') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('asprak.jadwal') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>JADWAL PRAKTIKUM</span>
                    </a>
                    <a href="{{ route('asprak.rps') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('asprak.rps') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>RPS & BAHAN AJAR</span>
                    </a>
                    <a href="{{ route('asprak.riwayat') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('asprak.riwayat*') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>RIWAYAT & REKAP</span>
                        @if($badgeCount > 0)
                            <span class="w-5 h-5 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center">
                                {{ $badgeCount }}
                            </span>
                        @endif
                    </a>

                @elseif($role === 'dosen')
                    <a href="{{ route('dosen.dashboard') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('dosen.dashboard') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>DASHBOARD</span>
                    </a>
                    <a href="{{ route('dosen.jadwal') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('dosen.jadwal') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>JADWAL PRAKTIKUM</span>
                    </a>
                    <a href="{{ route('dosen.monitoring') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('dosen.monitoring*') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>MONITORING</span>
                        @if($badgeCount > 0)
                            <span class="w-5 h-5 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center">
                                {{ $badgeCount }}
                            </span>
                        @endif
                    </a>

                @elseif($role === 'koor_asprak')
                    <a href="{{ route('koor.dashboard') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('koor.dashboard') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>DASHBOARD</span>
                    </a>
                    <a href="{{ route('koor.jadwal') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('koor.jadwal') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>JADWAL PRAKTIKUM</span>
                    </a>
                    <a href="{{ route('koor.monitoring') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('koor.monitoring*') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>MONITORING</span>
                        @if($badgeCount > 0)
                            <span class="w-5 h-5 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center">
                                {{ $badgeCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('koor.rps') }}"
                       class="flex items-center justify-between px-6 py-3 text-xs font-bold tracking-wide transition
                              {{ request()->routeIs('koor.rps') ? 'bg-slate-200/80 text-[#08244B] border-l-4 border-[#F97316]' : 'text-[#08244B] hover:bg-slate-100' }}">
                        <span>RPS & BAHAN AJAR</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- Tombol Logout di Bawah Sidebar -->
        <div class="p-5 border-t border-slate-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full py-2 px-4 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- AREA KONTEN KANAN -->
    <main class="flex-1 p-7 overflow-y-auto">
        <!-- Tanggal Hari Ini di Pojok Kanan Atas -->
        <div class="flex justify-end mb-2">
            <p class="text-xs font-bold text-slate-800">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}
            </p>
        </div>

        <!-- Header Box -->
        <header class="bg-white rounded-2xl shadow-sm border border-slate-100 px-7 py-4 mb-6 flex items-center justify-between">
            <h1 class="text-xl font-extrabold text-[#08244B]">
                @yield('header_title')
            </h1>

            <!-- Foto Profil Lingkaran -->
            <div class="w-11 h-11 rounded-full overflow-hidden border-2 border-slate-200 bg-[#08244B] flex items-center justify-center text-white font-bold text-sm shrink-0">
                @if(auth()->user()->photo)
                    <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Profil" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
        </header>

        <!-- Konten Dinamis Halaman -->
        @yield('content')
    </main>

</body>
</html>