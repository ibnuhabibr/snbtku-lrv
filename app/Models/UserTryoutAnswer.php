<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTryoutAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_tryout_id',
        'question_id',
        'user_answer',
        'is_correct',
        'is_flagged',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'is_flagged' => 'boolean',
    ];

    /**
     * Get the user tryout that owns this answer.
     */
    public function userTryout()
    {
        return $this->belongsTo(UserTryout::class);
    }

    /**
     * Get the question for this answer.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Scope a query to only include correct answers.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope a query to only include incorrect answers.
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }
}
