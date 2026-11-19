@extends('layouts.app', ['badgeCount' => $missedCount])

@section('title', 'Dashboard Asprak - ASISKU')
@section('header_title', 'Halo, ' . $user->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- KOLOM KIRI (Kartu Profil & Riwayat) -->
    <div class="lg:col-span-5 space-y-6">
        <!-- Kartu Data Pengguna -->
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

        <!-- Kartu Riwayat Notifikasi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-base font-extrabold text-[#08244B]">📋 Riwayat</span>
            </div>

            <div class="space-y-3.5 text-xs">
                @forelse($reports as $rep)
                    @php
                        $repDate = \Carbon\Carbon::parse($rep->session_date);
                        $isOngoing = $rep->status === 'pending'
                            && $repDate->isToday()
                            && $currentTime >= $rep->schedule->start_time
                            && $currentTime <= $rep->schedule->end_time;
                        $isMissed = $rep->status === 'pending' && !$isOngoing && (
                            $repDate->lt(\Carbon\Carbon::today('Asia/Jakarta')) ||
                            ($repDate->isToday() && $currentTime > $rep->schedule->end_time)
                        );
                    @endphp

                    <div>
                        <p class="font-bold text-slate-800">
                            [{{ $repDate->locale('id')->translatedFormat('l, d F Y') }}]
                        </p>
                        @if($rep->status === 'saved')
                            <p class="text-green-600 font-medium italic">
                                Berita Acara {{ $rep->schedule->course->name }} Berhasil Disimpan
                            </p>
                        @elseif($isMissed)
                            <p class="text-red-600 font-medium italic">
                                Berita Acara {{ $rep->schedule->course->name }} terlewat. Periksa <span class="font-bold not-italic">RIWAYAT & REKAP!</span>
                            </p>
                        @else
                            <p class="text-slate-700 font-medium italic">
                                Berita Acara {{ $rep->schedule->course->name }} Tersedia
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-slate-400 italic">Belum ada aktivitas berita acara.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN (Praktikum Terdekat & Jadwal Praktikum) -->
    <div class="lg:col-span-7 space-y-6">
        <!-- Kartu Praktikum Terdekat -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-base font-extrabold text-[#08244B]">🕒 Praktikum Terdekat</span>
            </div>

            @if($nearestSchedule)
                <a href="{{ route('asprak.riwayat') }}"
                   class="block bg-[#08244B] hover:bg-[#0d3366] transition rounded-xl px-5 py-3 flex items-center justify-between text-white">
                    <span class="text-xs font-bold">{{ $nearestSchedule->course->name }} ({{ $nearestSchedule->class_name }})</span>
                    <div class="flex items-center gap-3">
                        @if($isSessionActive)
                            <span class="w-4 h-4 rounded-full bg-green-500 inline-block animate-pulse" title="Sesi sedang berlangsung"></span>
                        @endif
                        <span class="bg-white text-[#08244B] text-xs font-extrabold px-4 py-1 rounded-lg">
                            {{ $nearestSchedule->room }}
                        </span>
                    </div>
                </a>

                @if($isSessionActive)
                    <p class="text-center text-[11px] font-semibold italic text-green-600 mt-2.5">
                        Sesi Pembuatan Berita Acara Tersedia. Klik untuk membuat
                    </p>
                @else
                    <p class="text-center text-[11px] text-slate-500 mt-2.5">
                        Jadwal: {{ $nearestSchedule->day }}, {{ substr($nearestSchedule->start_time, 0, 5) }} - {{ substr($nearestSchedule->end_time, 0, 5) }}
                    </p>
                @endif
            @else
                <p class="text-xs text-slate-400">Belum ada jadwal praktikum yang ditugaskan.</p>
            @endif
        </div>

        <!-- Kartu Tabel Jadwal Praktikum -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-base font-extrabold text-[#08244B]">📅 Jadwal Praktikum</span>
            </div>

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
                        @forelse($schedules as $sch)
                            @php
                                $isRunning = ($sch->day === $todayName) && ($currentTime >= $sch->start_time && $currentTime <= $sch->end_time);
                            @endphp
                            <tr class="{{ $isRunning ? 'bg-sky-100 font-semibold' : 'bg-white' }}">
                                <td class="border border-slate-400 py-3 px-3">{{ $sch->day }}</td>
                                <td class="border border-slate-400 py-3 px-3">
                                    {{ $sch->course->name }} ({{ $sch->class_name }})
                                </td>
                                <td class="border border-slate-400 py-3 px-3">
                                    {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="border border-slate-400 py-4 text-slate-400">Tidak ada jadwal.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection