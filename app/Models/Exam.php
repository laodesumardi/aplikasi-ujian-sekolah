<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'subject_id',
        'class_id',
        'kelas',
        'title',
        'description',
        'exam_date',
        'start_time',
        'duration',
        'total_questions',
        'total_points',
        'status',
        'created_by',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRelation(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_question')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('exam_question.order');
    }

    public function getKelasNameAttribute()
    {
        return $this->classRelation ? $this->classRelation->name : $this->kelas;
    }

    public function getTotalQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Sync total_questions and total_points based on attached questions
     */
    public function syncQuestionStats()
    {
        $questions = $this->questions()->get();
        $totalQuestions = $questions->count();
        $totalPoints = $questions->sum('points') ?? $totalQuestions; // Default 1 point per question if not set
        
        $this->update([
            'total_questions' => $totalQuestions,
            'total_points' => $totalPoints,
        ]);
    }
}
