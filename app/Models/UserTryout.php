<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTryout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tryout_package_id',
        'start_time',
        'end_time',
        'status',
        'score',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'score' => 'decimal:2',
    ];

    /**
     * Get the user that owns this tryout.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tryout package for this tryout.
     */
    public function tryoutPackage()
    {
        return $this->belongsTo(TryoutPackage::class);
    }

    /**
     * Get the answers for this tryout.
     */
    public function userTryoutAnswers()
    {
        return $this->hasMany(UserTryoutAnswer::class);
    }

    /**
     * Get the subtest progresses for this tryout.
     * Relasi ke 7 progres subtes
     */
    public function subtestProgresses()
    {
        return $this->hasMany(UserSubtestProgress::class)
                    ->join('subjects', 'user_subtest_progress.subject_id', '=', 'subjects.id')
                    ->select('user_subtest_progress.*', 'subjects.subtest_order') // Pastikan subtest_order di-select
                    ->orderBy('subjects.subtest_order'); // Pastikan order by ini ada
    }

    /**
     * Scope a query to only include ongoing tryouts.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    /**
     * Scope a query to only include completed tryouts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get the duration of the tryout in minutes.
     */
    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInMinutes($this->end_time);
        }
        return null;
    }
}
