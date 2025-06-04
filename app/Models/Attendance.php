<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'subject_advised_id',
        'instructor_id',
        'first_name',
        'last_name',
        'date',
        'time_in',
        'time_out',
        'status',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function subjectAdvised()
    {
        return $this->hasMany(SubjectAdvised::class, 'id', 'subject_advised_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
