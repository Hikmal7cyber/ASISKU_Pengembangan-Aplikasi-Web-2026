<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['code', 'name', 'rps_file'];

    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }

    public function materials()
    {
        return $this->hasMany(WeeklyMaterial::class);
    }
}