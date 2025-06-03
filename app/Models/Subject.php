<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'subject_code',
        'subject_name',
        'description',
        'program_id',
        'year_level',
        'section'
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, "subject_student");
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
}
