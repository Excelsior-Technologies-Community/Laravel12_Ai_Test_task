<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_name',
        'trainee_email',
        'title',
        'description',
        'role',
        'level',
        'duration_hours',
        'test_instructions',
        'status',
        'score',
        'review_notes',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];
}