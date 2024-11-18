<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    protected $fillable = [
        'task_id',
        'text',
        'completed',
        'expanded'
    ];

    protected $casts = [
        'completed' => 'boolean'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}