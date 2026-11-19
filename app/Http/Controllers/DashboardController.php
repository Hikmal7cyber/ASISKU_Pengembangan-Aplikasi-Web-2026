<?php

namespace App\Http\Controllers;

use App\Models\CourseSchedule;
use App\Models\SessionReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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

    public function asprak()
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $todayName = $this->getHariIndonesia($now);
        $currentTime = $now->format('H:i:s');

        // 1. Ambil jadwal milik asprak yang sedang login
        $schedules = $user->schedulesAsAsprak()->with('course')->get();
        $scheduleIds = $schedules->pluck('id');

        // 2. Hitung berita acara terlewat (untuk badge merah di sidebar)
        $missedReports = SessionReport::with(['schedule.course', 'material'])
            ->whereIn('schedule_id', $scheduleIds)
            ->where('status', 'pending')
            ->where(function ($q) use ($now, $currentTime) {
                $q->whereDate('session_date', '<', $now->toDateString())
                  ->orWhere(function ($q2) use ($now, $currentTime) {
                      $q2->whereDate('session_date', $now->toDateString())
                         ->whereHas('schedule', fn($s) => $s->where('end_time', '<', $currentTime));
                  });
            })
            ->get();

        $missedCount = $missedReports->count();

        // 3. Tentukan Praktikum Terdekat & cek apakah sesi sedang berjalan (titik hijau)
        $todaySchedules = $schedules->where('day', $todayName)->sortBy('start_time');
        $nearestSchedule = $todaySchedules->first() ?? $schedules->first();
        $isSessionActive = false;
        $activeReport = null;

        if ($nearestSchedule && $nearestSchedule->day === $todayName) {
            if ($currentTime >= $nearestSchedule->start_time && $currentTime <= $nearestSchedule->end_time) {
                $isSessionActive = true;
            }
            // Cari report hari ini jika ada
            $activeReport = SessionReport::where('schedule_id', $nearestSchedule->id)
                ->whereDate('session_date', $now->toDateString())
                ->first();
        }

        // 4. Susun Riwayat Notifikasi (Terbaru di atas)
        $reports = SessionReport::with(['schedule.course', 'material'])
            ->whereIn('schedule_id', $scheduleIds)
            ->orderByDesc('session_date')
            ->take(5)
            ->get();

        return view('asprak.dashboard', compact(
            'user',
            'now',
            'todayName',
            'currentTime',
            'schedules',
            'nearestSchedule',
            'isSessionActive',
            'activeReport',
            'reports',
            'missedCount'
        ));
    }

    public function dosen()
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $todayName = $this->getHariIndonesia($now);
        $currentTime = $now->format('H:i:s');

        // Jadwal mata kuliah yang diampu dosen
        $schedules = CourseSchedule::with('course')
            ->where('dosen_id', $user->id)
            ->orderBy('start_time')
            ->get();

        // Kelas yang berjalan di hari ini
        $todaySchedules = $schedules->where('day', $todayName);

        // Hitung catatan asisten yang belum dibaca (badge merah Monitoring)
        $unreadNotesCount = SessionReport::whereIn('schedule_id', $schedules->pluck('id'))
            ->where('status', 'saved')
            ->where('is_read_by_dosen', false)
            ->count();

        // Riwayat pembuatan berita acara oleh asisten
        $reports = SessionReport::with(['schedule.course'])
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->where('status', 'saved')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        return view('dosen.dashboard', compact(
            'user',
            'now',
            'todayName',
            'currentTime',
            'schedules',
            'todaySchedules',
            'reports',
            'unreadNotesCount'
        ));
    }

    public function koor()
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $todayName = $this->getHariIndonesia($now);
        $currentTime = $now->format('H:i:s');

        // Co. Asprak memantau seluruh jadwal mata kuliah terkait
        $schedules = CourseSchedule::with('course')
            ->orderBy('start_time')
            ->get();

        $todaySchedules = $schedules->where('day', $todayName);

        // Hitung catatan asisten yang belum dibaca (badge merah Monitoring)
        $unreadNotesCount = SessionReport::whereIn('schedule_id', $schedules->pluck('id'))
            ->where('status', 'saved')
            ->where('is_read_by_dosen', false)
            ->count();

        // Riwayat pembuatan berita acara
        $reports = SessionReport::with(['schedule.course'])
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->where('status', 'saved')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        return view('koor.dashboard', compact(
            'user',
            'now',
            'todayName',
            'currentTime',
            'schedules',
            'todaySchedules',
            'reports',
            'unreadNotesCount'
        ));
    }
}