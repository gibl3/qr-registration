<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    /** @use HasFactory<\Database\Factories\ProgramFactory> */
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'abbreviation',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
