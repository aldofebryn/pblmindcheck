<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['nomor', 'teks_id', 'teks_en', 'subskala'];
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
