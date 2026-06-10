<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ChecksScreeningCooldown;
use App\Models\Patient;
use App\Models\Screening;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    use ChecksScreeningCooldown;

    // ── Dashboard utama pasien ────────────────────────────────────
    public function index()
    {
        $id_pasien = session('patient_id');
        if (! $id_pasien) return redirect()->route('patient.login');

        $patient = Patient::find($id_pasien);
        if (! $patient) {
            session()->forget('patient_id');
            return redirect()->route('patient.login');
        }

        // Gunakan selesai_at untuk urutan kronologis yang benar
        $screenings = Screening::where('patient_id', $id_pasien)
            ->with('result')
            ->whereNotNull('selesai_at')
            ->orderByDesc('selesai_at')
            ->get();

        $lastResult = $screenings->first()?->result;

        // Data chart tren (maks 10 sesi terbaru, dibalik agar kronologis)
        $chartData = $screenings->take(10)->reverse()->values()->map(function ($s) {
            return [
                'tanggal'   => $s->selesai_at->format('d M'),
                'depresi'   => $s->result?->skor_depresi   ?? 0,
                'kecemasan' => $s->result?->skor_kecemasan ?? 0,
                'stres'     => $s->result?->skor_stres     ?? 0,
            ];
        });

        // Status cooldown — dihitung dari controller, bukan flash session
        $screenCheck    = $this->canScreen($id_pasien);
        $canScreenNow   = $screenCheck['can'];
        $nextScreenDate = $screenCheck['next']?->format('d F Y');

        $resumeMinutes = max(1, (int) Cache::remember('setting_resume_minutes', 3600, function () {
            return (int) Setting::getValue('screening_resume_minutes', 30);
        }));

        $defaultSeconds = $resumeMinutes * 60;

        $activeDraft = Screening::where('patient_id', $id_pasien)
            ->whereNull('selesai_at')
            ->with(['answers.question:id,nomor'])
            ->latest()
            ->first();

        $activeDraftMeta = null;

        if ($activeDraft) {
            $remainingSeconds = $activeDraft->remaining_seconds ?? $defaultSeconds;

            if ($activeDraft->timer_started_at) {
                $elapsed = $activeDraft->timer_started_at->diffInSeconds(now());
                $remainingSeconds -= $elapsed;
            }

            $remainingSeconds = max(0, (int) $remainingSeconds);

            if ($remainingSeconds <= 0) {
                $activeDraft->answers()->delete();
                $activeDraft->delete();
                $activeDraft = null;
            } else {
                if (! $activeDraft->timer_started_at) {
                    $activeDraft->update([
                        'remaining_seconds' => $remainingSeconds,
                        'timer_started_at'  => now(),
                        'last_activity_at'  => now(),
                    ]);

                    $activeDraft->refresh();
                    $activeDraft->load(['answers.question:id,nomor']);
                } else {
                    $activeDraft->update([
                        'remaining_seconds' => $remainingSeconds,
                    ]);
                }

                $answeredNumbers = $activeDraft->answers
                    ->map(fn ($answer) => optional($answer->question)->nomor)
                    ->filter()
                    ->values();

                $lastQuestionNumber = $answeredNumbers->max();
                $answeredCount = $answeredNumbers->count();

                if ($lastQuestionNumber && $activeDraft->last_answered_question !== $lastQuestionNumber) {
                    $activeDraft->update([
                        'last_answered_question' => $lastQuestionNumber,
                    ]);
                }

                $activeDraftMeta = [
                    'last_question_number' => $lastQuestionNumber,
                    'answered_count'       => $answeredCount,
                    'remaining_seconds'    => $remainingSeconds,
                    'expired_at_text'      => now()->addSeconds($remainingSeconds)->format('H:i') . ' WIB',
                    'last_activity_text'   => optional($activeDraft->last_activity_at)->format('d F Y, H:i') . ' WIB',
                ];
            }
        }

        return view('patient.dashboard', compact(
            'screenings', 'id_pasien', 'chartData', 'patient',
            'lastResult', 'canScreenNow', 'nextScreenDate', 'activeDraft', 'activeDraftMeta'
        ));
    }

    // ── Halaman pengaturan profil ─────────────────────────────────
    public function settings()
    {
        $id_pasien = session('patient_id');
        if (! $id_pasien) return redirect()->route('patient.login');

        $patient = Patient::find($id_pasien);
        if (! $patient) {
            session()->forget('patient_id');
            return redirect()->route('patient.login');
        }

        return view('patient.settings', compact('patient'));
    }

    // ── Update profil & password ──────────────────────────────────
    public function updateSettings(Request $request)
    {
        $id_pasien = session('patient_id');
        if (! $id_pasien) return redirect()->route('patient.login');

        $patient = Patient::find($id_pasien);
        if (! $patient) {
            session()->forget('patient_id');
            return redirect()->route('patient.login');
        }

        $request->validate([
            'username'         => ['required', 'max:255', 'regex:/^[a-zA-Z ]+$/', 'unique:patients,username,' . $patient->id],
            'umur'             => 'required|integer|min:1|max:120',
            'status_pekerjaan' => 'required|string',
            'alias'            => 'nullable|string|max:100',
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf dan spasi.',
            'username.unique' => 'Username sudah digunakan, pilih yang lain.',
        ]);

        $patient->username         = $request->username;
        $patient->umur             = $request->umur;
        $patient->status_pekerjaan = $request->status_pekerjaan;
        $patient->alias            = $request->alias;

        if ($request->filled('old_password')) {
            if (! Hash::check($request->old_password, $patient->password)) {
                return back()->with('error', 'Password lama tidak sesuai.');
            }
            if (! $request->filled('new_password')) {
                return back()->with('error', 'Password baru wajib diisi jika password lama diisi.');
            }
            $patient->password = Hash::make($request->new_password);
        }

        $patient->save();
        return back()->with('success', 'Data profil berhasil diperbarui.');
    }
}
