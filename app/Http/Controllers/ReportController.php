<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\SessionReport;
use App\Models\Student;
use App\Models\WeeklyMaterial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // 1. Halaman Riwayat & Rekap Asisten Praktikum
    public function asprakIndex(Request $request)
    {
        $user = Auth::user();
        $courses = $user->schedulesAsAsprak()
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id')
            ->values();

        $selectedCourseId = $request->query('course_id');
        $selectedCourse = null;
        $reports = collect();
        $now = Carbon::now('Asia/Jakarta');
        $currentTime = $now->format('H:i:s');

        if ($selectedCourseId) {
            $selectedCourse = Course::find($selectedCourseId);

            // Ambil jadwal asprak pada mata kuliah yang dipilih
            $schedules = $user->schedulesAsAsprak()
                ->where('course_id', $selectedCourseId)
                ->get();

            // Sinkronisasi otomatis: pastikan setiap materi pekanan punya baris report untuk jadwal ini
            $materials = WeeklyMaterial::where('course_id', $selectedCourseId)
                ->orderBy('week_number')
                ->get();

            foreach ($schedules as $sch) {
                foreach ($materials as $mat) {
                    SessionReport::firstOrCreate(
                        [
                            'schedule_id' => $sch->id,
                            'material_id' => $mat->id,
                        ],
                        [
                            'session_date' => $now->toDateString(),
                            'status'       => 'pending',
                        ]
                    );
                }
            }

            $reports = SessionReport::with(['schedule.course', 'material'])
                ->whereIn('schedule_id', $schedules->pluck('id'))
                ->get()
                ->sortBy(fn($r) => $r->material->week_number);
        }

        return view('asprak.riwayat', compact(
            'courses',
            'selectedCourseId',
            'selectedCourse',
            'reports',
            'now',
            'currentTime'
        ));
    }

    // 2. Halaman Formulir Berita Acara & Absensi
    public function edit(SessionReport $report)
    {
        $report->load(['schedule.course', 'schedule.aspraks', 'material', 'attendances.student']);

        // Pastikan seluruh mahasiswa di kelas tersebut terdaftar pada presensi laporan ini
        $students = Student::where('class_name', $report->schedule->class_name)
            ->orderBy('nim')
            ->get();

        foreach ($students as $student) {
            Attendance::firstOrCreate(
                [
                    'report_id'  => $report->id,
                    'student_id' => $student->id,
                ],
                [
                    'status' => 'H',
                ]
            );
        }

        $report->load(['attendances.student']);

        // Hitung persentase kehadiran individu berdasarkan seluruh sesi 'saved' pada kelas ini
        $savedReportIds = SessionReport::where('schedule_id', $report->schedule_id)
            ->where('status', 'saved')
            ->pluck('id');

        $totalSavedSessions = $savedReportIds->count();
        $percentages = [];

        foreach ($report->attendances as $att) {
            if ($totalSavedSessions === 0) {
                $percentages[$att->student_id] = 100;
            } else {
                $hadirCount = Attendance::whereIn('report_id', $savedReportIds)
                    ->where('student_id', $att->student_id)
                    ->where('status', 'H')
                    ->count();

                $percentages[$att->student_id] = round(($hadirCount / $totalSavedSessions) * 100);
            }
        }

        return view('asprak.berita-acara', compact('report', 'percentages'));
    }

    // 3. Simpan / Kirim Berita Acara & Absensi
    public function update(Request $request, SessionReport $report)
    {
        $validated = $request->validate([
            'present_asprak'  => 'nullable|string',
            'notes_for_dosen' => 'nullable|string',
            'issues'          => 'nullable|string',
            'attendances'     => 'required|array',
            'attendances.*'   => 'in:H,S,I,A',
        ]);

        // Update status kehadiran setiap praktikan
        foreach ($validated['attendances'] as $attendanceId => $status) {
            Attendance::where('id', $attendanceId)
                ->where('report_id', $report->id)
                ->update(['status' => $status]);
        }

        // Jika ada catatan untuk dosen, tandai belum dibaca agar muncul notif merah di dashboard Dosen/Koor
        $hasNotes = !empty(trim($validated['notes_for_dosen'] ?? ''));

        $report->update([
            'present_asprak'   => $validated['present_asprak'],
            'notes_for_dosen'  => $validated['notes_for_dosen'],
            'issues'           => $validated['issues'],
            'is_read_by_dosen' => $hasNotes ? false : true,
            'status'           => 'saved',
        ]);

        return redirect()->route('asprak.riwayat', ['course_id' => $report->schedule->course_id])
            ->with('success', 'Berita Acara Pekan ' . $report->material->week_number . ' berhasil disimpan (SAVED).');
    }
}