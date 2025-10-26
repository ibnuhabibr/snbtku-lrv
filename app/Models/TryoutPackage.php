<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TryoutPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'duration_minutes',
        'status',
    ];

    /**
     * Get the questions for this tryout package.
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'tryout_package_question')
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderBy('pivot_order');
    }

    /**
     * Get the user tryouts for this package.
     */
    public function userTryouts()
    {
        return $this->hasMany(UserTryout::class);
    }

    /**
     * Scope a query to only include published packages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Get the total number of questions in this package.
     */
    public function getTotalQuestionsAttribute()
    {
        return $this->questions()->count();
    }
}
