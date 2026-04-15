<?php
// app/Models/ExamAnswer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $table = 'exam_answers';

    protected $fillable = [
        'exam_id',
        'question_id',
        'user_id',
        'answer',
        'is_correct',
        'score'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'score' => 'decimal:2'
    ];

    // Relasi
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
