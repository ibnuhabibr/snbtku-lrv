<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubtestProgress extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'user_subtest_progress';

    protected $fillable = [
        'user_tryout_id',
        'subject_id',
        'status',
        'score',
        'time_remaining_seconds',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    // Relasi ke user_tryout (induk)
    public function userTryout()
    {
        return $this->belongsTo(UserTryout::class);
    }

    // Relasi ke subject (subtes)
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}