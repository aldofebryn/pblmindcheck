<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['screening_id', 'question_id', 'nilai'];
    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
