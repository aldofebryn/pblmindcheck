<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ChecksScreeningCooldown;
use App\Models\Screening;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;
use App\Models\Setting;
use App\Services\DecisionTreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ScreeningController extends Controller
{
    use ChecksScreeningCooldown;

    private function resumeSeconds(): int
    {
        $minutes = Cache::remember('setting_resume_minutes', 3600, function () {
            return (int) Setting::getValue('screening_resume_minutes', 30);
        });

        return max(1, $minutes) * 60;
    }

    private function calculateRemainingSeconds(Screening $screening, int $defaultSeconds): int
    {
        $remaining = $screening->remaining_seconds ?? $defaultSeconds;

        if ($screening->timer_started_at) {
            $elapsed = $screening->timer_started_at->diffInSeconds(now());
            $remaining -= $elapsed;
        }

        return max(0, (int) $remaining);
    }

    private function deleteExpiredDraft(Screening $screening): void
    {
        $screening->answers()->delete();
        $screening->delete();
    }

    private function pauseDraftTimer(Screening $screening, int $defaultSeconds): bool
    {
        $remaining = $this->calculateRemainingSeconds($screening, $defaultSeconds);

        if ($remaining <= 0) {
            $this->deleteExpiredDraft($screening);
            return false;
        }

        $screening->update([
            'remaining_seconds' => $remaining,
            'timer_started_at'  => null,
            'last_activity_at'  => now(),
        ]);

        return true;
    }

    private function startDraftTimer(Screening $screening, int $defaultSeconds): bool
    {
        $remaining = $this->calculateRemainingSeconds($screening, $defaultSeconds);

        if ($remaining <= 0) {
            $this->deleteExpiredDraft($screening);
            return false;
        }

        $screening->update([
            'remaining_seconds' => $remaining,
            'timer_started_at'  => now(),
            'last_activity_at'  => now(),
        ]);

        return true;
    }

    // ── Tampilkan form skrining ───────────────────────────────────
    public function show()
    {
        $id_pasien = session('patient_id');
        if (! $id_pasien) return redirect()->route('patient.login');

        $check = $this->canScreen($id_pasien);
        if (! $check['can']) {
            return redirect()->route('patient.dashboard')
                ->with('screening_locked', true)
                ->with('screening_next', $check['next']->format('d F Y'));
        }

        $defaultSeconds = $this->resumeSeconds();

        $draft = Screening::where('patient_id', $id_pasien)
            ->whereNull('selesai_at')
            ->latest()
            ->first();

        if ($draft) {
            if (! $this->pauseDraftTimer($draft, $defaultSeconds)) {
                return redirect()
                    ->route('patient.dashboard')
                    ->with('screening_expired', 'Sesi sudah kadaluwarsa. Silakan mulai skrining dari awal.');
            }

            $draft->refresh();
        }

        if (! $draft) {
            $draft = Screening::create([
                'patient_id'              => $id_pasien,
                'started_at'              => now(),
                'last_activity_at'        => now(),
                'remaining_seconds'       => $defaultSeconds,
                'timer_started_at'        => null,
                'last_answered_question'  => null,
            ]);
        }

        $questions = Cache::remember('dass21_questions', 3600, function () {
            return Question::orderBy('nomor')->get();
        });

        $questionsFormatted = $questions->map(fn($q) => [
            'id'       => $q->nomor,
            'teks_id'  => $q->teks_id,
            'teks_en'  => $q->teks_en,
            'subskala' => $q->subskala,
        ])->values();

        $savedAnswers = Answer::where('screening_id', $draft->id)
            ->with('question:id,nomor')
            ->get()
            ->mapWithKeys(fn($a) => [$a->question->nomor => $a->nilai]);

        return view('patient.screening', compact(
            'questions',
            'questionsFormatted',
            'draft',
            'savedAnswers'
        ));
    }

    public function autosave(Request $request)
    {
        $id_pasien = session('patient_id');
        if (! $id_pasien) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'question_number' => 'required|integer|min:1|max:21',
            'value'           => 'required|integer|min:0|max:3',
        ]);

        $defaultSeconds = $this->resumeSeconds();

        $screening = Screening::where('patient_id', $id_pasien)
            ->whereNull('selesai_at')
            ->latest()
            ->first();

        if ($screening) {
            if (! $this->pauseDraftTimer($screening, $defaultSeconds)) {
                return response()->json([
                    'expired'  => true,
                    'message'  => 'Sesi telah berakhir, mohon ulangi screening.',
                    'redirect' => route('patient.dashboard'),
                ], 419);
            }

            $screening->refresh();
        }

        if (! $screening) {
            $screening = Screening::create([
                'patient_id'              => $id_pasien,
                'started_at'              => now(),
                'last_activity_at'        => now(),
                'remaining_seconds'       => $defaultSeconds,
                'timer_started_at'        => null,
                'last_answered_question'  => null,
            ]);
        }

        $qMap = Cache::remember('dass21_question_map', 3600, function () {
            return Question::orderBy('nomor')->pluck('id', 'nomor');
        });

        $questionId = $qMap[$request->question_number] ?? null;
        if (! $questionId) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        Answer::updateOrCreate(
            ['screening_id' => $screening->id, 'question_id' => $questionId],
            ['nilai' => $request->value]
        );

        $screening->update([
            'last_activity_at'       => now(),
            'last_answered_question' => $request->question_number,
            'timer_started_at'       => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban tersimpan',
        ]);
    }

    public function leave(Request $request)
    {
        $id_pasien = session('patient_id');

        if (! $id_pasien) {
            return response()->json(['success' => false], 401);
        }

        $defaultSeconds = $this->resumeSeconds();

        $screening = Screening::where('patient_id', $id_pasien)
            ->whereNull('selesai_at')
            ->latest()
            ->first();

        if (! $screening) {
            return response()->json(['success' => true]);
        }

        if (! $this->startDraftTimer($screening, $defaultSeconds)) {
            return response()->json([
                'success' => false,
                'expired' => true,
            ], 419);
        }

        return response()->json(['success' => true]);
    }

    // ── Submit jawaban → decision tree → simpan ───────────────────
    public function submit(Request $request)
    {
        $id_pasien = session('patient_id');
        if (! $id_pasien) return redirect()->route('patient.login');

        $check = $this->canScreen($id_pasien);
        if (! $check['can']) {
            return redirect()->route('patient.dashboard')
                ->with('screening_locked', true)
                ->with('screening_next', $check['next']->format('d F Y'));
        }

        $request->validate([
            'answers'   => 'required|array|size:21',
            'answers.*' => 'required|integer|min:0|max:3',
        ], [
            'answers.size'  => 'Semua 21 pertanyaan wajib dijawab.',
            'answers.*.min' => 'Nilai jawaban tidak valid (0–3).',
            'answers.*.max' => 'Nilai jawaban tidak valid (0–3).',
        ]);

        $defaultSeconds = $this->resumeSeconds();

        $screening = Screening::where('patient_id', $id_pasien)
            ->whereNull('selesai_at')
            ->latest()
            ->first();

        if ($screening) {
            if (! $this->pauseDraftTimer($screening, $defaultSeconds)) {
                return redirect()
                    ->route('patient.dashboard')
                    ->with('screening_expired', 'Sesi sudah kadaluwarsa. Silakan mulai skrining dari awal.');
            }

            $screening->refresh();
        }

        if (! $screening) {
            $screening = Screening::create([
                'patient_id'              => $id_pasien,
                'started_at'              => now(),
                'last_activity_at'        => now(),
                'remaining_seconds'       => $defaultSeconds,
                'timer_started_at'        => null,
                'last_answered_question'  => null,
            ]);
        }

        $screening->update([
            'selesai_at'              => now(),
            'last_activity_at'        => now(),
            'remaining_seconds'       => 0,
            'timer_started_at'        => null,
            'last_answered_question'  => null,
        ]);

        // Pakai cached question map
        $qMap = Cache::remember('dass21_question_map', 3600, function () {
            return Question::orderBy('nomor')->pluck('id', 'nomor');
        });

        foreach ($request->answers as $nomor => $nilai) {
            Answer::updateOrCreate(
                ['screening_id' => $screening->id, 'question_id' => $qMap[(int) $nomor]],
                ['nilai'        => (int) $nilai]
            );
        }

        $dt    = new DecisionTreeService();
        $hasil = $dt->process(
            collect($request->answers)
                ->mapWithKeys(fn($v, $k) => [(int) $k => $v])
                ->toArray()
        );

        Result::create([
            'screening_id'   => $screening->id,
            'skor_depresi'   => $hasil['skor_d'],
            'skor_kecemasan' => $hasil['skor_a'],
            'skor_stres'     => $hasil['skor_s'],
            'kat_depresi'    => $hasil['kat_d'],
            'kat_kecemasan'  => $hasil['kat_a'],
            'kat_stres'      => $hasil['kat_s'],
            'rekomendasi'    => $hasil['rekomendasi'],
        ]);

        return redirect()->route('hasil', $screening->id);
    }

    // ── Halaman hasil ─────────────────────────────────────────────
    public function hasil(Screening $screening)
    {
        $id_pasien = session('patient_id');
        if (! $id_pasien) return redirect()->route('patient.login');
        if ($screening->patient_id != $id_pasien) abort(403);

        $result = $screening->result;
        if (! $result) abort(404);

        $teks = DecisionTreeService::tekstRek($result->rekomendasi);

        $riwayat = Screening::where('patient_id', $screening->patient_id)
            ->whereNotNull('selesai_at')
            ->with('result')
            ->orderBy('selesai_at', 'desc')
            ->limit(5)
            ->get()
            ->reverse()
            ->values();

        $lineData = [
            'labels'    => $riwayat->map(fn($s) => $s->selesai_at->format('d/m'))->toArray(),
            'depresi'   => $riwayat->map(fn($s) => $s->result?->skor_depresi   ?? 0)->toArray(),
            'kecemasan' => $riwayat->map(fn($s) => $s->result?->skor_kecemasan ?? 0)->toArray(),
            'stres'     => $riwayat->map(fn($s) => $s->result?->skor_stres     ?? 0)->toArray(),
        ];

        $barData = [
            'labels'     => ['Depresi', 'Kecemasan', 'Stres'],
            'scores'     => [$result->skor_depresi,  $result->skor_kecemasan,  $result->skor_stres],
            'categories' => [$result->kat_depresi,   $result->kat_kecemasan,   $result->kat_stres],
        ];

        $prev = Screening::where('patient_id', $screening->patient_id)
            ->where('id', '<', $screening->id)
            ->with('result')
            ->whereNotNull('selesai_at')
            ->orderByDesc('id')
            ->first();

        $nextScreeningDate = $screening->selesai_at->addDays(7);

        return view('patient.hasil', compact(
            'screening', 'result', 'teks', 'prev', 'lineData', 'barData', 'nextScreeningDate'
        ));
    }
}