<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = ['nomor', 'teks_id', 'teks_en', 'subskala'];

    protected $casts = [
        'nomor' => 'integer',
    ];

    // Relasi ke jawaban (jika ada)
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    // Scope untuk filter subskala
    public function scopeSubskala($query, $subskala)
    {
        if ($subskala && $subskala !== 'all') {
            return $query->where('subskala', $subskala);
        }
        return $query;
    }
}