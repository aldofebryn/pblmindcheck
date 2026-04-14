<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\DecisionTreeService;

class Result extends Model
{
    protected $fillable = [
        'screening_id',
        'skor_depresi',
        'skor_kecemasan',
        'skor_stres',
        'kat_depresi',
        'kat_kecemasan',
        'kat_stres',
        'rekomendasi',
    ];

    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    public function badgeD()
    {
        return DecisionTreeService::badgeClass($this->kat_depresi);
    }
    public function badgeA()
    {
        return DecisionTreeService::badgeClass($this->kat_kecemasan);
    }
    public function badgeS()
    {
        return DecisionTreeService::badgeClass($this->kat_stres);
    }
    public function pctD()
    {
        return round($this->skor_depresi   / 42 * 100);
    }
    public function pctA()
    {
        return round($this->skor_kecemasan / 42 * 100);
    }
    public function pctS()
    {
        return round($this->skor_stres     / 42 * 100);
    }

    public function rekLabel(): string
    {
        return match ($this->rekomendasi) {
            'R16' => 'Perlu Konsultasi Segera',
            'R17' => 'Disarankan Konsultasi',
            'R18' => 'Pantau Mandiri',
            default => '-',
        };
    }

    public function rekBadge(): string
    {
        return match ($this->rekomendasi) {
            'R16' => 'bg-red-50 text-red-700 border-red-200',
            'R17' => 'bg-amber-50 text-amber-700 border-amber-200',
            'R18' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            default => '',
        };
    }
}
