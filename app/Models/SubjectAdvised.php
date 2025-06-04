<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectAdvised extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    
    protected $table = 'subject_advised';

    protected $fillable = [
        'subject_id',
        'instructor_id',
        'program_id',
        'year_level',
        'section',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    
    public function students()
    {
        return $this->belongsToMany(Student::class, 'subject_student', 'subject_advised_id', 'student_id');
    }
}
