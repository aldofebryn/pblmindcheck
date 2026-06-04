<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Screening;

/**
 * Menyediakan helper canScreen() untuk cek cooldown 7 hari skrining.
 * Digunakan oleh Patient\ScreeningController dan Patient\DashboardController.
 */
trait ChecksScreeningCooldown
{
    private function canScreen(int $patientId): array
    {
        $last = Screening::where('patient_id', $patientId)
            ->whereNotNull('selesai_at')
            ->orderByDesc('selesai_at')
            ->first();

        if (! $last) return ['can' => true, 'next' => null, 'last' => null];

        $nextDate = $last->selesai_at->addDays(7);
        return [
            'can'  => now()->gte($nextDate),
            'next' => $nextDate,
            'last' => $last->selesai_at,
        ];
    }
}
