<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionReport extends Model
{
    protected $fillable = [
        'schedule_id', 'material_id', 'session_date', 'present_asprak',
        'notes_for_dosen', 'issues', 'is_read_by_dosen', 'status'
    ];

    public function schedule()
    {
        return $this->belongsTo(CourseSchedule::class, 'schedule_id');
    }

    public function material()
    {
        return $this->belongsTo(WeeklyMaterial::class, 'material_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'report_id');
    }
}