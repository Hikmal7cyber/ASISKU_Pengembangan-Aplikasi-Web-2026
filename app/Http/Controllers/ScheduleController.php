<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    private function getHariIndonesia(Carbon $date): string
    {
        $days = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];

        return $days[$date->format('l')];
    }

    // 1. Halaman Jadwal untuk Asisten Praktikum (Read-Only)
    public function asprakIndex()
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $todayName = $this->getHariIndonesia($now);
        $currentTime = $now->format('H:i:s');

        $schedules = $user->schedulesAsAsprak()
            ->with(['course', 'dosen'])
            ->get();

        return view('asprak.jadwal', compact('schedules', 'todayName', 'currentTime'));
    }

    // 2. Halaman Jadwal untuk Dosen Koordinator (Tambah + & Edit)
    public function dosenIndex(Request $request)
    {
        $courses = Course::all();
        $selectedCourseId = $request->query('course_id');
        $schedules = collect();

        if ($selectedCourseId) {
            $schedules = CourseSchedule::with(['course', 'aspraks'])
                ->where('dosen_id', Auth::id())
                ->where('course_id', $selectedCourseId)
                ->orderBy('class_name')
                ->get();
        }

        $aspraks = User::where('role', 'asprak')->orderBy('name')->get();

        return view('dosen.jadwal', compact('courses', 'selectedCourseId', 'schedules', 'aspraks'));
    }

    // Proses Tambah Kelas Baru oleh Dosen
    public function dosenStore(Request $request)
    {
        $validated = $request->validate([
            'course_id'  => 'required|exists:courses,id',
            'class_name' => 'required|string|max:50',
            'day'        => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required',
            'room'       => 'required|string|max:50',
            'asprak_ids' => 'nullable|array',
            'asprak_ids.*' => 'exists:users,id',
        ]);

        $schedule = CourseSchedule::create([
            'course_id'  => $validated['course_id'],
            'dosen_id'   => Auth::id(),
            'class_name' => $validated['class_name'],
            'day'        => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'],
            'room'       => $validated['room'],
        ]);

        if (!empty($validated['asprak_ids'])) {
            $schedule->aspraks()->sync($validated['asprak_ids']);
        }

        return redirect()->route('dosen.jadwal', ['course_id' => $validated['course_id']])
            ->with('success', 'Kelas praktikum baru berhasil ditambahkan.');
    }

    // Proses Edit Jadwal & Plotting Asprak oleh Dosen
    public function dosenUpdate(Request $request, CourseSchedule $schedule)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:50',
            'day'        => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required',
            'room'       => 'required|string|max:50',
            'asprak_ids' => 'nullable|array',
            'asprak_ids.*' => 'exists:users,id',
        ]);

        $schedule->update([
            'class_name' => $validated['class_name'],
            'day'        => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'],
            'room'       => $validated['room'],
        ]);

        $schedule->aspraks()->sync($validated['asprak_ids'] ?? []);

        return redirect()->route('dosen.jadwal', ['course_id' => $schedule->course_id])
            ->with('success', 'Jadwal kelas ' . $schedule->class_name . ' berhasil diperbarui.');
    }

    // 3. Halaman Jadwal untuk Koordinator Asprak (Hanya Edit, Tanpa Tambah)
    public function koorIndex(Request $request)
    {
        $courses = Course::all();
        $selectedCourseId = $request->query('course_id');
        $schedules = collect();

        if ($selectedCourseId) {
            $schedules = CourseSchedule::with(['course', 'aspraks'])
                ->where('course_id', $selectedCourseId)
                ->orderBy('class_name')
                ->get();
        }

        $aspraks = User::where('role', 'asprak')->orderBy('name')->get();

        return view('koor.jadwal', compact('courses', 'selectedCourseId', 'schedules', 'aspraks'));
    }

    // Proses Edit Jadwal & Plotting Asprak oleh Co. Asprak
    public function koorUpdate(Request $request, CourseSchedule $schedule)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:50',
            'day'        => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required',
            'room'       => 'required|string|max:50',
            'asprak_ids' => 'nullable|array',
            'asprak_ids.*' => 'exists:users,id',
        ]);

        $schedule->update([
            'class_name' => $validated['class_name'],
            'day'        => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'],
            'room'       => $validated['room'],
        ]);

        $schedule->aspraks()->sync($validated['asprak_ids'] ?? []);

        return redirect()->route('koor.jadwal', ['course_id' => $schedule->course_id])
            ->with('success', 'Jadwal kelas ' . $schedule->class_name . ' berhasil diperbarui.');
    }
}