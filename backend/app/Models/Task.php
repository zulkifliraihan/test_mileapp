<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use App\Enums\TaskStatus;

class Task extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'status' => TaskStatus::class,
        ];
    }
}
