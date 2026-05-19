<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    protected $fillable = ['patient_id', 'selesai_at'];
    protected $casts    = ['selesai_at' => 'datetime'];

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
