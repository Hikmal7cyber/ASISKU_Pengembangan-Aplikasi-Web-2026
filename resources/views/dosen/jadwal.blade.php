@extends('layouts.app')

@section('title', 'Jadwal Praktikum Dosen - ASISKU')
@section('header_title', 'JADWAL PRAKTIKUM')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-7">

    @if(session('success'))
        <div class="mb-5 p-3.5 rounded-xl bg-green-50 border border-green-200 text-green-700 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Mata Kuliah & Tombol Tambah -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <form method="GET" action="{{ route('dosen.jadwal') }}" class="w-full max-w-xs">
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

        @if($selectedCourseId)
            <button type="button" onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                    class="px-5 py-2.5 rounded-xl bg-[#08244B] hover:bg-[#0d3366] text-white text-xs font-bold shadow transition cursor-pointer">
                Tambah +
            </button>
        @endif
    </div>

    @if(!$selectedCourseId)
        <div class="py-16 text-center text-sm font-medium text-slate-400">
            belum ada mata kuliah dipilih
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-slate-400 text-xs text-center">
                <thead>
                    <tr class="bg-slate-300 text-slate-800 font-bold">
                        <th class="border border-slate-400 py-3 px-3">Hari</th>
                        <th class="border border-slate-400 py-3 px-3">Mata Kuliah</th>
                        <th class="border border-slate-400 py-3 px-3">Jam</th>
                        <th class="border border-slate-400 py-3 px-3">Kelas</th>
                        <th class="border border-slate-400 py-3 px-3">Ruang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $sch)
                        <tr class="bg-white">
                            <td class="border border-slate-400 py-3 px-3">{{ $sch->day }}</td>
                            <td class="border border-slate-400 py-3 px-3">{{ $sch->course->name }} ({{ $sch->class_name }})</td>
                            <td class="border border-slate-400 py-3 px-3">{{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}</td>
                            <td class="border border-slate-400 py-3 px-3">{{ $sch->class_name }}</td>
                            <td class="border border-slate-400 py-3 px-3">{{ $sch->room }}</td>
                        </tr>
                        <tr class="bg-slate-50">
                            <td colspan="5" class="border border-slate-400 py-2.5 px-4">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-slate-700 font-medium text-left">
                                        @if($sch->aspraks->count() > 0)
                                            {{ $sch->aspraks->map(fn($a) => $a->name . ' (' . $a->nim_nip . ')')->join(', ') }}
                                        @else
                                            <span class="italic text-slate-400">Belum ada asisten praktikum di-assign</span>
                                        @endif
                                    </span>
                                    <button type="button"
                                            onclick="openEditModal({{ $sch->id }}, '{{ $sch->class_name }}', '{{ $sch->day }}', '{{ substr($sch->start_time, 0, 5) }}', '{{ substr($sch->end_time, 0, 5) }}', '{{ $sch->room }}', {{ json_encode($sch->aspraks->pluck('id')) }})"
                                            class="px-4 py-1.5 rounded-lg bg-[#F97316] hover:bg-orange-600 text-white text-[11px] font-bold flex items-center gap-1 shrink-0 cursor-pointer">
                                        Edit ✎
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-slate-400 py-6 text-slate-400">
                                Belum ada jadwal kelas untuk mata kuliah ini. Klik <strong>Tambah +</strong> untuk membuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- MODAL TAMBAH KELAS BARU -->
@if($selectedCourseId)
<div id="modalTambah" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-base font-extrabold text-[#08244B] mb-4">Tambah Kelas Praktikum</h3>
        <form action="{{ route('dosen.jadwal.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kelas (Misal: TI-D)</label>
                    <input type="text" name="class_name" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ruang (Misal: G1.4)</label>
                    <input type="text" name="room" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Hari</label>
                <select name="day" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                        <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Assign Asisten Praktikum</label>
                <div class="max-h-32 overflow-y-auto border border-slate-300 rounded-lg p-2.5 space-y-1.5">
                    @foreach($aspraks as $asp)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="asprak_ids[]" value="{{ $asp->id }}" class="rounded">
                            <span>{{ $asp->name }} ({{ $asp->nim_nip }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold cursor-pointer">Batal</button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-[#08244B] text-white font-bold cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- MODAL EDIT JADWAL -->
<div id="modalEdit" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-base font-extrabold text-[#08244B] mb-4">Edit Jadwal Praktikum</h3>
        <form id="formEdit" method="POST" class="space-y-3.5 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kelas</label>
                    <input type="text" id="edit_class_name" name="class_name" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ruang</label>
                    <input type="text" id="edit_room" name="room" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Hari</label>
                <select id="edit_day" name="day" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                        <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jam Mulai</label>
                    <input type="time" id="edit_start_time" name="start_time" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jam Selesai</label>
                    <input type="time" id="edit_end_time" name="end_time" required class="w-full px-3 py-2 rounded-lg border border-slate-300">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Asisten Praktikum</label>
                <div class="max-h-32 overflow-y-auto border border-slate-300 rounded-lg p-2.5 space-y-1.5">
                    @foreach($aspraks as $asp)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="asprak_ids[]" value="{{ $asp->id }}" class="edit-asprak-checkbox rounded">
                            <span>{{ $asp->name }} ({{ $asp->nim_nip }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold cursor-pointer">Batal</button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-[#F97316] text-white font-bold cursor-pointer">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, className, day, startTime, endTime, room, assignedIds) {
        document.getElementById('formEdit').action = '/dosen/jadwal/' + id;
        document.getElementById('edit_class_name').value = className;
        document.getElementById('edit_day').value = day;
        document.getElementById('edit_start_time').value = startTime;
        document.getElementById('edit_end_time').value = endTime;
        document.getElementById('edit_room').value = room;

        document.querySelectorAll('.edit-asprak-checkbox').forEach(cb => {
            cb.checked = assignedIds.includes(parseInt(cb.value));
        });

        document.getElementById('modalEdit').classList.remove('hidden');
    }
</script>
@endsection