<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'correct_answer',
        'explanation',
    ];

    /**
     * Get the topic that owns this question.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the tryout packages that include this question.
     */
    public function tryoutPackages()
    {
        return $this->belongsToMany(TryoutPackage::class, 'tryout_package_question')
                    ->withPivot('order')
                    ->withTimestamps();
    }

    /**
     * Get the user answers for this question.
     */
    public function userTryoutAnswers()
    {
        return $this->hasMany(UserTryoutAnswer::class);
    }

    /**
     * Get all answer options as an array.
     */
    public function getOptionsAttribute()
    {
        return [
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
            'e' => $this->option_e,
        ];
    }
}
