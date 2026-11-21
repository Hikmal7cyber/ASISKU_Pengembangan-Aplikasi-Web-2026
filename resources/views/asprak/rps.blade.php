@extends('layouts.app')

@section('title', 'RPS & Bahan Ajar - ASISKU')
@section('header_title', 'RPS & BAHAN AJAR')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-7">

    <!-- Dropdown Sortir Mata Kuliah -->
    <div class="mb-6">
        <form method="GET" action="{{ route('asprak.rps') }}" class="w-full max-w-xs">
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
        <!-- Banner Rencana Pembelajaran Semester (RPS) -->
        <div class="bg-[#08244B] rounded-2xl px-7 py-4 mb-6 flex flex-wrap items-center justify-between gap-4 text-white shadow">
            <h2 class="text-sm md:text-base font-extrabold">
                Rencana Pembelajaran Semester (RPS)
            </h2>

            @if($selectedCourse->rps_file)
                <a href="{{ asset('storage/' . $selectedCourse->rps_file) }}" target="_blank" download
                   class="px-7 py-2 rounded-xl bg-white hover:bg-slate-100 text-[#08244B] text-xs font-extrabold shadow transition">
                    Unduh
                </a>
            @else
                <span class="px-4 py-2 rounded-xl bg-white/15 text-white/80 text-xs font-semibold italic">
                    Belum diunggah
                </span>
            @endif
        </div>

        <!-- Tabel Bahan Ajar Pekanan -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-slate-400 text-xs text-center">
                <thead>
                    <tr class="bg-slate-300 text-slate-800 font-bold">
                        <th class="border border-slate-400 py-3 px-3 w-20">Pekan</th>
                        <th class="border border-slate-400 py-3 px-4">Mata Kuliah</th>
                        <th class="border border-slate-400 py-3 px-4">Materi</th>
                        <th class="border border-slate-400 py-3 px-3 w-28">Kelas</th>
                        <th class="border border-slate-400 py-3 px-4 w-36">Bahan Ajar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $mat)
                        <tr class="bg-white hover:bg-slate-50">
                            <td class="border border-slate-400 py-3 px-3 font-semibold">{{ $mat->week_number }}</td>
                            <td class="border border-slate-400 py-3 px-4">{{ $selectedCourse->name }}</td>
                            <td class="border border-slate-400 py-3 px-4">{{ $mat->topic }}</td>
                            <td class="border border-slate-400 py-3 px-3">{{ $classNames }}</td>
                            <td class="border border-slate-400 py-3 px-4">
                                @if($mat->material_file)
                                    <a href="{{ asset('storage/' . $mat->material_file) }}" target="_blank" download
                                       class="text-[#08244B] font-bold underline hover:text-[#F97316]">
                                        File PDF
                                    </a>
                                @else
                                    <span class="text-slate-400 font-bold">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-slate-400 py-6 text-slate-400">
                                Belum ada materi pekanan yang ditambahkan oleh Koordinator Asprak.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection