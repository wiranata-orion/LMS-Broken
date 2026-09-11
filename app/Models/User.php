<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)->withPivot('enrolled_at')->withTimestamps();
    }
}
