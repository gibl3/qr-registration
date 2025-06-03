<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'department_id',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
