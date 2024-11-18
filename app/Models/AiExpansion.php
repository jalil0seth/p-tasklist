<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiExpansion extends Model
{
    protected $table = 'ai_expansions';

    protected $fillable = [
        'target_id',
        'target_type',
        'content'
    ];

    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}