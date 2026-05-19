<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\GuardsAdmin;
use App\Models\{Patient, Screening, Result};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use GuardsAdmin;

    public function index()
    {
        $this->guardAdmin();

        $totalSesi  = Screening::whereNotNull('selesai_at')->count();
        $totalUser  = Screening::whereNotNull('selesai_at')->distinct('patient_id')->count('patient_id');

        $r16 = Result::where('rekomendasi', 'R16')->count();
        $r17 = Result::where('rekomendasi', 'R17')->count();
        $r18 = Result::where('rekomendasi', 'R18')->count();

        $alertCount = Screening::whereHas('result', fn($q) => $q->where('rekomendasi', 'R16'))
            ->where('selesai_at', '>=', now()->subHours(48))
            ->whereNotNull('selesai_at')
            ->count();

        // ── Aktivitas pengguna ────────────────────────────────────
        $userHarian = collect(range(6, 0))->map(function ($i) {
            $tgl = now()->subDays($i);
            return [
                'label' => $tgl->format('d M'),
                'count' => Screening::whereDate('selesai_at', $tgl)
                    ->whereNotNull('selesai_at')
                    ->distinct('patient_id')
                    ->count('patient_id'),
            ];
        });

        $userMingguan = collect(range(3, 0))->map(function ($i) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end   = now()->subWeeks($i)->endOfWeek();
            return [
                'label' => 'Minggu ' . $start->format('d M'),
                'count' => Screening::whereBetween('selesai_at', [$start, $end])
                    ->whereNotNull('selesai_at')
                    ->distinct('patient_id')
                    ->count('patient_id'),
            ];
        });

        $userBulanan = collect(range(11, 0))->map(function ($i) {
            $bulan = now()->subMonths($i);
            return [
                'label' => $bulan->format('M Y'),
                'count' => Screening::whereMonth('selesai_at', $bulan->month)
                    ->whereYear('selesai_at', $bulan->year)
                    ->whereNotNull('selesai_at')
                    ->distinct('patient_id')
                    ->count('patient_id'),
            ];
        });

        // ── Distribusi kategori — 3 query groupBy ─────
        $katLabels = ['Normal', 'Ringan', 'Sedang', 'Berat', 'Sangat Berat'];
        $katColors = ['#10b981', '#0ea5e9', '#f59e0b', '#f97316', '#ef4444'];

        $depresiRaw   = Result::select('kat_depresi',   DB::raw('count(*) as total'))->groupBy('kat_depresi')->pluck('total', 'kat_depresi');
        $kecemasnRaw  = Result::select('kat_kecemasan', DB::raw('count(*) as total'))->groupBy('kat_kecemasan')->pluck('total', 'kat_kecemasan');
        $stresRaw     = Result::select('kat_stres',     DB::raw('count(*) as total'))->groupBy('kat_stres')->pluck('total', 'kat_stres');

        $katDepresi   = array_map(fn($k) => $depresiRaw[$k]  ?? 0, $katLabels);
        $katKecemasan = array_map(fn($k) => $kecemasnRaw[$k] ?? 0, $katLabels);
        $katStres     = array_map(fn($k) => $stresRaw[$k]    ?? 0, $katLabels);

        $recent = Screening::with('result')
            ->whereNotNull('selesai_at')
            ->orderByDesc('selesai_at')
            ->take(10)->get();

        $topPatient = Patient::withCount(['screenings' => fn($q) => $q->whereNotNull('selesai_at')])
            ->orderByDesc('screenings_count')
            ->take(5)->get();

        return view('admin.dashboard', compact(
            'totalSesi', 'totalUser', 'r16', 'r17', 'r18', 'alertCount',
            'katLabels', 'katColors', 'katDepresi', 'katKecemasan', 'katStres',
            'recent', 'topPatient', 'userHarian', 'userMingguan', 'userBulanan'
        ));
    }
}
