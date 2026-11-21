@extends('layouts.app')

@section('title', 'Berita Acara Praktikum - ASISKU')
@section('header_title', 'RIWAYAT & REKAP > Berita Acara')

@section('content')
<form action="{{ route('asprak.berita-acara.update', $report->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-7">

        <!-- Banner Pekan & Tombol Kirim -->
        <div class="bg-[#08244B] rounded-2xl px-7 py-4 mb-7 flex flex-wrap items-center justify-between gap-4 text-white shadow">
            <h2 class="text-sm md:text-base font-extrabold">
                Pekan {{ $report->material->week_number }}: {{ $report->material->topic }}
            </h2>

            <button type="submit"
                    class="px-8 py-2 rounded-xl bg-white hover:bg-slate-100 text-[#08244B] text-xs font-extrabold shadow transition cursor-pointer">
                Kirim
            </button>
        </div>

        <!-- Judul Dokumen Berita Acara -->
        <h3 class="text-center text-base md:text-lg font-extrabold text-[#08244B] tracking-wide uppercase mb-6">
            BERITA ACARA PELAKSANAAN<br>PRAKTIKUM
        </h3>

        <!-- Daftar Asprak Hadir (Manual via Tombol Edit) & Tanggal -->
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-6 text-xs">
            <div class="flex-1">
                <div id="asprakDisplayBox" class="space-y-1 text-slate-800 font-semibold">
                    @if($report->present_asprak)
                        @foreach(explode("\n", $report->present_asprak) as $index => $line)
                            @if(trim($line) !== '')
                                <p>Asisten Praktikum {{ $index + 1 }}: <span class="font-normal">{{ trim($line) }}</span></p>
                            @endif
                        @endforeach
                    @else
                        <p class="text-slate-400 italic">Belum ada asisten praktikum yang dicatat hadir (Klik Edit untuk mengisi).</p>
                    @endif
                </div>

                <!-- Input Textarea Tersembunyi yang Muncul Saat Tombol Edit Ditekan -->
                <div id="asprakEditBox" class="hidden mt-2">
                    <label class="block font-bold text-[#08244B] mb-1">
                        Tuliskan Nama Asisten Praktikum yang Hadir (1 baris per asisten, misal: Nama (NIM)):
                    </label>
                    @php
                        $asprakPlaceholder = $report->schedule->aspraks->count() > 0
                            ? "Contoh:\n" . $report->schedule->aspraks->map(fn($a) => $a->name . ' (' . $a->nim_nip . ')')->join("\n")
                            : "Contoh:\nNama Asisten Praktikum (NIM)";
                    @endphp
                    <textarea name="present_asprak" id="present_asprak_input" rows="3"
                              placeholder="{{ $asprakPlaceholder }}"
                              class="w-full max-w-lg px-3 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:border-[#08244B]">{{ old('present_asprak', $report->present_asprak) }}</textarea>
                    <div class="mt-1.5">
                        <button type="button" onclick="toggleAsprakEdit(false)"
                                class="px-3 py-1 rounded bg-[#08244B] text-white text-[11px] font-bold cursor-pointer">
                            Selesai Edit Nama
                        </button>
                    </div>
                </div>

                <p class="mt-3 font-bold text-slate-800">
                    Date: <span class="font-semibold">{{ \Carbon\Carbon::parse($report->session_date)->locale('id')->translatedFormat('l, d F Y') }}</span>
                </p>
            </div>

            <div>
                <button type="button" onclick="toggleAsprakEdit(true)"
                        class="px-4 py-1.5 rounded-lg bg-[#F97316] hover:bg-orange-600 text-white text-[11px] font-bold inline-flex items-center gap-1 shadow-sm cursor-pointer">
                    Edit ✎
                </button>
            </div>
        </div>

        <!-- Tabel Presensi Mahasiswa -->
        <div class="overflow-x-auto mb-7">
            <table class="w-full border-collapse border border-slate-400 text-xs text-center">
                <thead>
                    <tr class="bg-slate-300 text-slate-800 font-bold">
                        <th class="border border-slate-400 py-2.5 px-2 w-12">No</th>
                        <th class="border border-slate-400 py-2.5 px-4">Nama Mahasiswa</th>
                        <th class="border border-slate-400 py-2.5 px-3">NIM</th>
                        <th class="border border-slate-400 py-2.5 px-3 w-24">Kelas</th>
                        <th class="border border-slate-400 py-2.5 px-2 w-12">H</th>
                        <th class="border border-slate-400 py-2.5 px-2 w-12">S</th>
                        <th class="border border-slate-400 py-2.5 px-2 w-12">I</th>
                        <th class="border border-slate-400 py-2.5 px-2 w-12">A</th>
                        <th class="border border-slate-400 py-2.5 px-3 w-16">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->attendances as $idx => $att)
                        @php
                            $pct = $percentages[$att->student_id] ?? 100;
                        @endphp
                        <tr class="bg-white hover:bg-slate-50">
                            <td class="border border-slate-400 py-2.5 px-2">{{ $idx + 1 }}</td>
                            <td class="border border-slate-400 py-2.5 px-4 text-left font-medium">{{ $att->student->name }}</td>
                            <td class="border border-slate-400 py-2.5 px-3">{{ $att->student->nim }}</td>
                            <td class="border border-slate-400 py-2.5 px-3">{{ $att->student->class_name }}</td>

                            @foreach(['H', 'S', 'I', 'A'] as $statusOption)
                                <td class="border border-slate-400 py-2.5 px-2">
                                    <input type="radio"
                                           name="attendances[{{ $att->id }}]"
                                           value="{{ $statusOption }}"
                                           {{ $att->status === $statusOption ? 'checked' : '' }}
                                           class="w-3.5 h-3.5 accent-[#08244B] cursor-pointer">
                                </td>
                            @endforeach

                            <td class="border border-slate-400 py-2.5 px-3 font-extrabold {{ $pct >= 80 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $pct }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Catatan untuk Dosen & Kendala Praktikum -->
        <div class="space-y-5 text-xs">
            <div>
                <label for="notes_for_dosen" class="block font-extrabold text-[#08244B] mb-1.5">
                    Catatan untuk Dosen:
                </label>
                <textarea id="notes_for_dosen" name="notes_for_dosen" rows="3"
                          placeholder="Tinggalkan catatan disini"
                          class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-800 focus:outline-none focus:border-[#08244B]">{{ old('notes_for_dosen', $report->notes_for_dosen) }}</textarea>
            </div>

            <div>
                <label for="issues" class="block font-extrabold text-[#08244B] mb-1.5">
                    Kendala Praktikum:
                </label>
                <textarea id="issues" name="issues" rows="3"
                          placeholder="Tinggalkan catatan disini"
                          class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-800 focus:outline-none focus:border-[#08244B]">{{ old('issues', $report->issues) }}</textarea>
            </div>
        </div>

    </div>
</form>

<script>
    function toggleAsprakEdit(showEdit) {
        const displayBox = document.getElementById('asprakDisplayBox');
        const editBox = document.getElementById('asprakEditBox');
        const input = document.getElementById('present_asprak_input');

        if (showEdit) {
            displayBox.classList.add('hidden');
            editBox.classList.remove('hidden');
            input.focus();
        } else {
            const lines = input.value.split('\n').filter(l => l.trim() !== '');
            if (lines.length > 0) {
                displayBox.innerHTML = lines.map((line, i) =>
                    `<p>Asisten Praktikum ${i + 1}: <span class="font-normal">${line.trim()}</span></p>`
                ).join('');
            } else {
                displayBox.innerHTML = `<p class="text-slate-400 italic">Belum ada asisten praktikum yang dicatat hadir (Klik Edit untuk mengisi).</p>`;
            }
            editBox.classList.add('hidden');
            displayBox.classList.remove('hidden');
        }
    }
</script>
@endsection