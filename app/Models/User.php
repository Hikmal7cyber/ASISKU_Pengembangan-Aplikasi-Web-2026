<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'username', 'nim_nip', 'role', 'photo', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function schedulesAsDosen()
    {
        return $this->hasMany(CourseSchedule::class, 'dosen_id');
    }

    public function schedulesAsAsprak()
    {
        return $this->belongsToMany(CourseSchedule::class, 'schedule_aspraks', 'user_id', 'schedule_id');
    }
}