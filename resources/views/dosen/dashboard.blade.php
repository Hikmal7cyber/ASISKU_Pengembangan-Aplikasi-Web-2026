@extends('layouts.app', ['badgeCount' => $unreadNotesCount])

@section('title', 'Dashboard Dosen - ASISKU')
@section('header_title', 'Halo, ' . $user->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- KOLOM KIRI (Kartu Profil & Riwayat) -->
    <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col items-center text-center">
            <div class="w-32 h-40 rounded-lg overflow-hidden bg-[#08244B] text-white flex items-center justify-center text-4xl font-extrabold mb-4 shadow">
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto Profil" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <h2 class="text-base font-bold text-[#08244B]">{{ $user->name }}</h2>
            <div class="mt-2 px-6 py-1.5 rounded-lg bg-slate-200 text-slate-700 text-xs font-semibold">
                {{ $user->nim_nip }}
            </div>
        </div>

        <!-- Riwayat Berita Acara dari Asprak -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-base font-extrabold text-[#08244B] mb-4">📋 Riwayat</h3>
            <div class="space-y-3.5 text-xs">
                @forelse($reports as $rep)
                    <div>
                        <p class="font-bold text-slate-800">
                            [{{ \Carbon\Carbon::parse($rep->session_date)->locale('id')->translatedFormat('l, d F Y') }}]
                        </p>
                        @if(!$rep->is_read_by_dosen && $rep->notes_for_dosen)
                            <p class="text-red-600 font-medium">
                                Catatan dari kelas {{ $rep->schedule->course->name }} ({{ $rep->schedule->class_name }}). Segera periksa <span class="font-bold">MONITORING!</span>
                            </p>
                        @else
                            <p class="text-slate-700">
                                Berita Acara {{ $rep->schedule->course->name }} ({{ $rep->schedule->class_name }}) telah dibuat
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-slate-400 italic">Belum ada riwayat berita acara.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN (Praktikum Berjalan & Jadwal Praktikum) -->
    <div class="lg:col-span-7 space-y-6">
        <!-- Praktikum Berjalan Hari Ini -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-base font-extrabold text-[#08244B] mb-4">🕒 Praktikum Berjalan</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-slate-400 text-xs text-center">
                    <thead>
                        <tr class="bg-slate-300 text-slate-800 font-bold">
                            <th class="border border-slate-400 py-2.5 px-3">Ruang</th>
                            <th class="border border-slate-400 py-2.5 px-3">Mata Kuliah</th>
                            <th class="border border-slate-400 py-2.5 px-3">Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todaySchedules as $sch)
                            @php
                                $isRunning = ($currentTime >= $sch->start_time && $currentTime <= $sch->end_time);
                            @endphp
                            <tr class="{{ $isRunning ? 'bg-sky-100 font-semibold' : 'bg-white' }}">
                                <td class="border border-slate-400 py-3 px-3">{{ $sch->room }}</td>
                                <td class="border border-slate-400 py-3 px-3">{{ $sch->course->name }} ({{ $sch->class_name }})</td>
                                <td class="border border-slate-400 py-3 px-3">{{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="border border-slate-400 py-4 text-slate-400">Tidak ada kelas praktikum hari ini ({{ $todayName }}).</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Jadwal Praktikum Mingguan -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-base font-extrabold text-[#08244B] mb-4">📅 Jadwal Praktikum</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-slate-400 text-xs text-center">
                    <thead>
                        <tr class="bg-slate-300 text-slate-800 font-bold">
                            <th class="border border-slate-400 py-2.5 px-3">Hari</th>
                            <th class="border border-slate-400 py-2.5 px-3">Mata Kuliah</th>
                            <th class="border border-slate-400 py-2.5 px-3">Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $sch)
                            <tr>
                                <td class="border border-slate-400 py-3 px-3">{{ $sch->day }}</td>
                                <td class="border border-slate-400 py-3 px-3">{{ $sch->course->name }} ({{ $sch->class_name }})</td>
                                <td class="border border-slate-400 py-3 px-3">{{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection