<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'subject_id',
        'created_by',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'level',
        'topic',
        'difficulty',
        'points',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
