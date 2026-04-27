<?php

namespace App\Http\Controllers;

use App\Models\{Admin, Patient, Question, Screening, Result};
use App\Services\DecisionTreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ── Auth guard sederhana (session-based) ─────────────────────
    private function guardAdmin(): void
    {
        if (! session('admin_id')) {
            abort(redirect()->route('admin.login'));
        }
    }

    // ── Login form ───────────────────────────────────────────────
    public function loginPage()
    {
        if (session('admin_id')) return redirect()->route('admin.dashboard');
        return view('admin.login');
    }

    // ── Proses login ─────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            session(['admin_id' => $admin->id, 'admin_name' => $admin->name]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->withInput();
    }

    // ── Logout ───────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->session()->forget(['admin_id', 'admin_name']);
        return redirect()->route('admin.login');
    }

    // ── Dashboard ────────────────────────────────────────────────
    public function dashboard()
    {
        $this->guardAdmin();

        $totalSesi  = Screening::whereNotNull('selesai_at')->count();
        $totalUser = Screening::whereNotNull('selesai_at')
            ->distinct('patient_token')
            ->count('patient_token');

        // Hitung kasus perlu perhatian (R16)
        $r16 = Result::where('rekomendasi', 'R16')->count();
        $r17 = Result::where('rekomendasi', 'R17')->count();
        $r18 = Result::where('rekomendasi', 'R18')->count();

        // Alert: kasus R16 dalam 48 jam terakhir
        $alertCount = Screening::whereHas('result', fn($q) => $q->where('rekomendasi', 'R16'))
            ->where('selesai_at', '>=', now()->subHours(48))
            ->whereNotNull('selesai_at')
            ->count();

        // tren  7 hari terakhir
        // $tren = collect(range(6, 0))->map(function ($i) {
        //    $tgl = now()->subDays($i);
        //    return [
        //        'label' => $tgl->format('d M'),
        //        'count' => Screening::whereDate('selesai_at', $tgl)->whereNotNull('selesai_at')->count(),
        //    ];
        // });
        
        // ================== VISUALISASI USER ==================
        // Harian (7 hari terakhir)
            $userHarian = collect(range(6, 0))->map(function ($i) {
                $tgl = now()->subDays($i);
            return [
                'label' => $tgl->format('d M'),
                'count' => Screening::whereDate('selesai_at', $tgl)
            ->whereNotNull('selesai_at')
            ->distinct('patient_token')
            ->count('patient_token'),
            ];
        });
        // Mingguan (4 minggu terakhir)
            $userMingguan = collect(range(3, 0))->map(function ($i) {
                $start = now()->subWeeks($i)->startOfWeek();
                $end   = now()->subWeeks($i)->endOfWeek();

            return [
                'label' => 'Minggu ' . $start->format('d M'),
                'count' => Screening::whereBetween('selesai_at', [$start, $end])
            ->whereNotNull('selesai_at')
            ->distinct('patient_token')
            ->count('patient_token'),
            ];
        });
        // Bulanan (6 bulan terakhir)
            $userBulanan = collect(range(5, 0))->map(function ($i) {
                $bulan = now()->subMonths($i);

            return [
                'label' => $bulan->format('M Y'),
                'count' => Screening::whereMonth('selesai_at', $bulan->month)
            ->whereYear('selesai_at', $bulan->year)
            ->whereNotNull('selesai_at')
            ->distinct('patient_token')
            ->count('patient_token'),
            ];
         });

        // Distribusi kategori depresi untuk chart
        $katLabels  = ['Normal', 'Ringan', 'Sedang', 'Berat', 'Sangat Berat'];
        $katColors  = ['#10b981', '#0ea5e9', '#f59e0b', '#f97316', '#ef4444'];
        $katDepresi   = array_map(fn($k) => Result::where('kat_depresi',   $k)->count(), $katLabels);
        $katKecemasan = array_map(fn($k) => Result::where('kat_kecemasan', $k)->count(), $katLabels);
        $katStres     = array_map(fn($k) => Result::where('kat_stres',     $k)->count(), $katLabels);

        // 10 hasil skrining terbaru
        $recent = Screening::with('result')
            ->whereNotNull('selesai_at')
            ->orderByDesc('created_at')
            ->take(10)->get();

        // Token dengan riwayat terbanyak
        $topToken = Patient::withCount(['screenings' => fn($q) => $q->whereNotNull('selesai_at')])
            ->orderByDesc('screenings_count')
            ->take(5)->get();

        return view('admin.dashboard', compact(
            'totalSesi',
            'totalUser',
            'r16',
            'r17',
            'r18',
            'alertCount',
            //'tren',
            'katLabels',
            'katColors',
            'katDepresi',
            'katKecemasan',
            'katStres',
            'recent',
            'topToken',
            'userHarian',
            'userMingguan',
            'userBulanan'
        ));
    }

    // ── Daftar pertanyaan DASS-21 (read-only) ────────────────────
    public function questions()
    {
        $this->guardAdmin();
        $questions = Question::orderBy('nomor')->get();
        return view('admin.questions', compact('questions'));
    }

    // ── Detail riwayat satu token anonim ─────────────────────────
    public function tokenDetail(string $token)
    {
        $this->guardAdmin();

        $screenings = Screening::where('patient_token', $token)
            ->with('result')
            ->whereNotNull('selesai_at')
            ->orderBy('created_at')
            ->get();

        if ($screenings->isEmpty()) abort(404);

        $chartData = $screenings->map(fn($s) => [
            'tanggal'   => $s->selesai_at->format('d M'),
            'depresi'   => $s->result?->skor_depresi   ?? 0,
            'kecemasan' => $s->result?->skor_kecemasan ?? 0,
            'stres'     => $s->result?->skor_stres     ?? 0,
        ]);

        return view('admin.token-detail', compact('token', 'screenings', 'chartData'));
    }

    // ── Pengaturan (read-only MVP) ───────────────────────────────
    public function settings()
    {
        $this->guardAdmin();
        $adminName = session('admin_name');
        return view('admin.settings', compact('adminName'));
    }
}
