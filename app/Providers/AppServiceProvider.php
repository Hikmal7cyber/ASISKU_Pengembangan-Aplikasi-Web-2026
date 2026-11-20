<?php

namespace App\Providers;

use App\Models\CourseSchedule;
use App\Models\SessionReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();
            $now = Carbon::now('Asia/Jakarta');
            $currentTime = $now->format('H:i:s');
            $badgeCount = 0;

            if ($user->role === 'asprak') {
                $scheduleIds = $user->schedulesAsAsprak()->pluck('course_schedules.id');
                $badgeCount = SessionReport::whereIn('schedule_id', $scheduleIds)
                    ->where('status', 'pending')
                    ->where(function ($q) use ($now, $currentTime) {
                        $q->whereDate('session_date', '<', $now->toDateString())
                          ->orWhere(function ($q2) use ($now, $currentTime) {
                              $q2->whereDate('session_date', $now->toDateString())
                                 ->whereHas('schedule', fn($s) => $s->where('end_time', '<', $currentTime));
                          });
                    })
                    ->count();
            } elseif ($user->role === 'dosen') {
                $scheduleIds = CourseSchedule::where('dosen_id', $user->id)->pluck('id');
                $badgeCount = SessionReport::whereIn('schedule_id', $scheduleIds)
                    ->where('status', 'saved')
                    ->where('is_read_by_dosen', false)
                    ->count();
            } elseif ($user->role === 'koor_asprak') {
                $scheduleIds = CourseSchedule::pluck('id');
                $badgeCount = SessionReport::whereIn('schedule_id', $scheduleIds)
                    ->where('status', 'saved')
                    ->where('is_read_by_dosen', false)
                    ->count();
            }

            $view->with('badgeCount', $badgeCount);
        });
    }
}