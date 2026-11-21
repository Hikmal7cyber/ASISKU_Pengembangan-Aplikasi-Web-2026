@extends('layouts.app')

@section('title', 'Kelola RPS & Bahan Ajar - ASISKU')
@section('header_title', 'RPS & BAHAN AJAR')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-7">

    @if(session('success'))
        <div class="mb-5 p-3.5 rounded-xl bg-green-50 border border-green-200 text-green-700 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-semibold">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Dropdown Sortir Mata Kuliah -->
    <div class="mb-6">
        <form method="GET" action="{{ route('koor.rps') }}" class="w-full max-w-xs">
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
        <!-- Banner Upload Rencana Pembelajaran Semester (RPS) -->
        <div class="bg-[#08244B] rounded-2xl px-7 py-4 mb-6 flex flex-wrap items-center justify-between gap-4 text-white shadow">
            <div>
                <h2 class="text-sm md:text-base font-extrabold">
                    Rencana Pembelajaran Semester (RPS)
                </h2>
                @if($selectedCourse->rps_file)
                    <a href="{{ asset('storage/' . $selectedCourse->rps_file) }}" target="_blank"
                       class="text-[11px] text-orange-300 underline hover:text-orange-200">
                        Lihat File RPS Saat Ini (PDF)
                    </a>
                @else
                    <p class="text-[11px] text-slate-300 italic">Belum ada file RPS diunggah</p>
                @endif
            </div>

            <button type="button" onclick="document.getElementById('modalRps').classList.remove('hidden')"
                    class="px-7 py-2 rounded-xl bg-white hover:bg-slate-100 text-[#08244B] text-xs font-extrabold shadow transition cursor-pointer">
                {{ $selectedCourse->rps_file ? 'Ganti RPS' : 'Tambah' }}
            </button>
        </div>

        <!-- Tombol Tambah Baris Materi -->
        <div class="flex justify-end mb-4">
            <button type="button" onclick="document.getElementById('modalTambahMateri').classList.remove('hidden')"
                    class="px-5 py-2 rounded-xl bg-[#08244B] hover:bg-[#0d3366] text-white text-xs font-bold shadow transition cursor-pointer">
                Tambah +
            </button>
        </div>

        <!-- Tabel Kelola Bahan Ajar -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-slate-400 text-xs text-center">
                <thead>
                    <tr class="bg-slate-300 text-slate-800 font-bold">
                        <th class="border border-slate-400 py-3 px-3 w-16">Pekan</th>
                        <th class="border border-slate-400 py-3 px-4">Mata Kuliah</th>
                        <th class="border border-slate-400 py-3 px-4">Materi</th>
                        <th class="border border-slate-400 py-3 px-3 w-20">Kelas</th>
                        <th class="border border-slate-400 py-3 px-4 w-64">Bahan Ajar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $mat)
                        <tr class="bg-white">
                            <td class="border border-slate-400 py-3 px-3 font-semibold">{{ $mat->week_number }}</td>
                            <td class="border border-slate-400 py-3 px-4">{{ $selectedCourse->name }}</td>
                            <td class="border border-slate-400 py-3 px-4">{{ $mat->topic }}</td>
                            <td class="border border-slate-400 py-3 px-3 font-semibold">All</td>
                            <td class="border border-slate-400 py-2.5 px-3">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    @if($mat->material_file)
                                        <a href="{{ asset('storage/' . $mat->material_file) }}" target="_blank"
                                           class="text-[#08244B] font-bold underline mr-1">PDF</a>
                                    @else
                                        <span class="text-slate-400 italic mr-1">Kosong</span>
                                    @endif

                                    <button type="button"
                                            onclick="openEditMaterialModal({{ $mat->id }}, {{ $mat->week_number }}, '{{ addslashes($mat->topic) }}')"
                                            class="px-3 py-1 rounded-lg bg-[#F97316] hover:bg-orange-600 text-white text-[11px] font-bold cursor-pointer">
                                        Edit ✎
                                    </button>

                                    <form action="{{ route('koor.rps.material.destroy', $mat->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus materi Pekan {{ $mat->week_number }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 rounded-lg bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold cursor-pointer">
                                            Delete 🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-slate-400 py-6 text-slate-400">
                                Belum ada materi pekanan. Klik <strong>Tambah +</strong> untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODAL UPLOAD RPS -->
        <div id="modalRps" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-base font-extrabold text-[#08244B] mb-4">Unggah File RPS (PDF)</h3>
                <form action="{{ route('koor.rps.upload', $selectedCourse->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Pilih Dokumen RPS (.pdf, maks 10MB)</label>
                        <input type="file" name="rps_file" accept="application/pdf" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('modalRps').classList.add('hidden')"
                                class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold cursor-pointer">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-[#08244B] text-white font-bold cursor-pointer">Unggah</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL TAMBAH MATERI PEKANAN -->
        <div id="modalTambahMateri" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-base font-extrabold text-[#08244B] mb-4">Tambah Materi & Bahan Ajar</h3>
                <form action="{{ route('koor.rps.material.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5 text-xs">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pekan Ke-</label>
                        <input type="number" name="week_number" min="1" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Topik / Materi</label>
                        <input type="text" name="topic" required placeholder="Contoh: Array 1 Dimensi"
                               class="w-full px-3 py-2 rounded-lg border border-slate-300">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">File Bahan Ajar (.pdf - Opsional)</label>
                        <input type="file" name="material_file" accept="application/pdf"
                               class="w-full px-3 py-2 rounded-lg border border-slate-300">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('modalTambahMateri').classList.add('hidden')"
                                class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold cursor-pointer">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-[#08244B] text-white font-bold cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT MATERI PEKANAN -->
        <div id="modalEditMateri" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-base font-extrabold text-[#08244B] mb-4">Edit Materi & Bahan Ajar</h3>
                <form id="formEditMateri" method="POST" enctype="multipart/form-data" class="space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pekan Ke-</label>
                        <input type="number" id="edit_week_number" name="week_number" min="1" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Topik / Materi</label>
                        <input type="text" id="edit_topic" name="topic" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Ganti File PDF (Biarkan kosong jika tidak diubah)</label>
                        <input type="file" name="material_file" accept="application/pdf"
                               class="w-full px-3 py-2 rounded-lg border border-slate-300">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('modalEditMateri').classList.add('hidden')"
                                class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-bold cursor-pointer">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-[#F97316] text-white font-bold cursor-pointer">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditMaterialModal(id, weekNumber, topic) {
                document.getElementById('formEditMateri').action = '/koor/rps/material/' + id;
                document.getElementById('edit_week_number').value = weekNumber;
                document.getElementById('edit_topic').value = topic;
                document.getElementById('modalEditMateri').classList.remove('hidden');
            }
        </script>
    @endif

</div>
@endsection