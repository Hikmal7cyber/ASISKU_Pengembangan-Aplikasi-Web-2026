@extends('layouts.app')

@section('title', 'Riwayat & Rekap - ASISKU')
@section('header_title', 'RIWAYAT & REKAP')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-7">

    @if(session('success'))
        <div class="mb-5 p-3.5 rounded-xl bg-green-50 border border-green-200 text-green-700 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Dropdown Sortir Mata Kuliah -->
    <div class="mb-6">
        <form method="GET" action="{{ route('asprak.riwayat') }}" class="w-full max-w-xs">
            <select name="course_id" onchange="this.form.submit()"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:border-[#08244B]">
                <option value="">Mata Kuliah</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if(!$selectedCourseId || !$selectedCourse)
        <div class="py-16 text-center text-sm font-medium text-slate-400">
            belum ada mata kuliah dipilih
        </div>
    @else
        <!-- Banner Riwayat & Rekap -->
        <div class="bg-[#08244B] rounded-2xl px-7 py-4 mb-6 flex flex-wrap items-center justify-between gap-4 text-white shadow">
            <h2 class="text-sm md:text-base font-extrabold">
                Riwayat & Rekap
            </h2>

            <a href="{{ route('asprak.riwayat.pdf', ['course_id' => $selectedCourse->id]) }}"
               class="px-7 py-2 rounded-xl bg-white hover:bg-slate-100 text-[#08244B] text-xs font-extrabold shadow transition">
                Unduh
            </a>
        </div>

        <!-- Tabel Riwayat Pelaksanaan -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-slate-400 text-xs text-center">
                <thead>
                    <tr class="bg-slate-300 text-slate-800 font-bold">
                        <th class="border border-slate-400 py-3 px-3 w-16">Pekan</th>
                        <th class="border border-slate-400 py-3 px-4">Mata Kuliah</th>
                        <th class="border border-slate-400 py-3 px-4">Materi</th>
                        <th class="border border-slate-400 py-3 px-3 w-24">Kelas</th>
                        <th class="border border-slate-400 py-3 px-4 w-36">Berita Acara</th>
                        <th class="border border-slate-400 py-3 px-4 w-32">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $rep)
                        @php
                            $repDate = \Carbon\Carbon::parse($rep->session_date);
                            $isMissed = $rep->status === 'pending' && (
                                $repDate->lt(\Carbon\Carbon::today('Asia/Jakarta')) ||
                                ($repDate->isToday() && $currentTime > $rep->schedule->end_time)
                            );
                        @endphp
                        <tr class="{{ $isMissed ? 'bg-red-700 text-white font-semibold' : 'bg-white text-slate-800' }}">
                            <td class="border border-slate-400 py-3 px-3 font-semibold">{{ $rep->material->week_number }}</td>
                            <td class="border border-slate-400 py-3 px-4">{{ $selectedCourse->name }}</td>
                            <td class="border border-slate-400 py-3 px-4">{{ $rep->material->topic }}</td>
                            <td class="border border-slate-400 py-3 px-3">{{ $rep->schedule->class_name }}</td>
                            <td class="border border-slate-400 py-3 px-4 font-bold">
                                @if($rep->status === 'saved')
                                    <span class="text-green-600">SAVED</span>
                                @else
                                    <span class="{{ $isMissed ? 'text-white' : 'text-slate-600' }}">Belum Dibuat</span>
                                @endif
                            </td>
                            <td class="border border-slate-400 py-2.5 px-3">
                                <a href="{{ route('asprak.berita-acara.edit', $rep->id) }}"
                                   class="inline-flex items-center justify-center gap-1 px-4 py-1.5 rounded-lg bg-[#F97316] hover:bg-orange-600 text-white text-[11px] font-bold shadow-sm transition">
                                    Ubah ✎
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-slate-400 py-6 text-slate-400">
                                Belum ada jadwal pertemuan untuk mata kuliah ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection