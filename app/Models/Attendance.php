<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['report_id', 'student_id', 'status'];

    public function report()
    {
        return $this->belongsTo(SessionReport::class, 'report_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}