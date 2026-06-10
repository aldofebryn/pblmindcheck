<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    protected $fillable = [
        'patient_id',
        'started_at',
        'last_activity_at',
        'selesai_at',
        'timer_started_at',
        'remaining_seconds',
        'last_answered_question',
    ];

    protected $casts = [
        'selesai_at' => 'datetime',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'timer_started_at' => 'datetime',
        'remaining_seconds' => 'integer',
        'last_answered_question' => 'integer',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function result()
    {
        return $this->hasOne(Result::class);
    }
}
