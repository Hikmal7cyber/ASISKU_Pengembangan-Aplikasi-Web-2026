<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    protected $fillable = [
        'course_id', 'dosen_id', 'class_name', 'day', 'start_time', 'end_time', 'room'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function aspraks()
    {
        return $this->belongsToMany(User::class, 'schedule_aspraks', 'schedule_id', 'user_id');
    }

    public function reports()
    {
        return $this->hasMany(SessionReport::class, 'schedule_id');
    }
}