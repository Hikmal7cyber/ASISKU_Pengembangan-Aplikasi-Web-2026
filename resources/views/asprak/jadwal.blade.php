@extends('layouts.app')

@section('title', 'Jadwal Praktikum - ASISKU')
@section('header_title', 'JADWAL PRAKTIKUM')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-7">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-slate-400 text-xs text-center">
            <thead>
                <tr class="bg-slate-300 text-slate-800 font-bold">
                    <th class="border border-slate-400 py-3 px-4">Hari</th>
                    <th class="border border-slate-400 py-3 px-4">Mata Kuliah</th>
                    <th class="border border-slate-400 py-3 px-4">Jam</th>
                    <th class="border border-slate-400 py-3 px-4">Kelas</th>
                    <th class="border border-slate-400 py-3 px-4">Ruang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $sch)
                    @php
                        $isRunning = ($sch->day === $todayName) && ($currentTime >= $sch->start_time && $currentTime <= $sch->end_time);
                    @endphp
                    <tr class="{{ $isRunning ? 'bg-sky-100 font-semibold' : 'bg-white' }}">
                        <td class="border border-slate-400 py-3 px-4">{{ $sch->day }}</td>
                        <td class="border border-slate-400 py-3 px-4">{{ $sch->course->name }}</td>
                        <td class="border border-slate-400 py-3 px-4">{{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}</td>
                        <td class="border border-slate-400 py-3 px-4">{{ $sch->class_name }}</td>
                        <td class="border border-slate-400 py-3 px-4">{{ $sch->room }}</td>
                    </tr>
                    <tr class="{{ $isRunning ? 'bg-sky-50' : 'bg-slate-50' }}">
                        <td colspan="5" class="border border-slate-400 py-2 px-4 text-slate-700 font-medium">
                            {{ $sch->dosen->name }} ({{ $sch->dosen->nim_nip }})
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border border-slate-400 py-6 text-slate-400">
                            Belum ada jadwal praktikum yang ditugaskan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection