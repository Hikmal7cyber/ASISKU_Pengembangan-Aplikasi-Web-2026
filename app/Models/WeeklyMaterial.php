<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyMaterial extends Model
{
    protected $fillable = ['course_id', 'week_number', 'topic', 'material_file'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function reports()
    {
        return $this->hasMany(SessionReport::class, 'material_id');
    }
}