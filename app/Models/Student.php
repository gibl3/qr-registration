<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'program_id',
        'year_level',
        'section',
        'gender',
        'student_id',
        'email_address',
    ];

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucwords(strtolower($value));
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucfirst(strtolower($value));
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email_address');
    }

    public function createUserAccount($password)
    {
        return $this->user()->create([
            'name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email_address,
            'password' => Hash::make($password),
            'role' => 'student'
        ]);
    }

    public function subjects()
    {
        return $this->belongsToMany(SubjectAdvised::class, 'subject_student', 'student_id', 'subject_advised_id')
            ->withTimestamps();
    }

    public function realSubjects(): Subject
    {
        return $this->subjects->map(function($advised) {
            return $advised->subject; // assuming you have subject() in SubjectAdvised
        })->filter();
    }
}
