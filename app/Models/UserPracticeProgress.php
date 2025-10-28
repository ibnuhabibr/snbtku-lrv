<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPracticeProgress extends Model
{
    protected $table = 'user_practice_progress';
    protected $fillable = ['user_id', 'topic_id', 'completed_questions', 'last_viewed_question_id', 'completed_questions_list', 'questions_order'];

    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }
    
    public function topic() 
    { 
        return $this->belongsTo(Topic::class); 
    }
    
    public function lastViewedQuestion() 
    { 
        return $this->belongsTo(Question::class, 'last_viewed_question_id'); 
    }
}
