<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ChecksScreeningCooldown;
use App\Models\Patient;
use App\Models\Screening;
use App\Models\Setting;
use Illuminate\Http\Request;
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

        $resumeMinutes = max(1, (int) Setting::getValue('screening_resume_minutes', 30));

        $activeDraft = Screening::where('patient_id', $id_pasien)
            ->whereNull('selesai_at')
            ->with(['answers.question:id,nomor'])
            ->latest('last_activity_at')
            ->first();

        $activeDraftMeta = null;

        if ($activeDraft) {
            $lastActivityAt = $activeDraft->last_activity_at
                ?? $activeDraft->updated_at
                ?? $activeDraft->created_at
                ?? now();

            $expiredAt = $lastActivityAt->copy()->addMinutes($resumeMinutes);

            if ($expiredAt->lte(now())) {
                $activeDraft->answers()->delete();
                $activeDraft->delete();
                $activeDraft = null;
            } else {
                $lastAnswer = $activeDraft->answers
                    ->sortByDesc('updated_at')
                    ->first();

                $lastQuestionNumber = $lastAnswer?->question?->nomor;
                $answeredCount = $activeDraft->answers->count();

                $activeDraftMeta = [
                    'last_question_number' => $lastQuestionNumber,
                    'answered_count'        => $answeredCount,
                    'remaining_seconds'     => (int) max(0, now()->diffInSeconds($expiredAt, false)),
                    'expired_at_text'       => $expiredAt->format('H:i') . ' WIB',
                    'last_activity_text'    => $lastActivityAt->format('d F Y, H:i') . ' WIB',
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
