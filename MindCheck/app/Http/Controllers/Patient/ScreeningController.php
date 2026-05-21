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
use Illuminate\Support\Facades\Session;

class ScreeningController extends Controller
{
    use ChecksScreeningCooldown;

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

        $resumeMinutes = (int) Setting::getValue('screening_resume_minutes', 30);

        $draft = Screening::where('patient_id', $id_pasien)
            ->whereNull('selesai_at')
            ->latest()
            ->first();

        if ($draft && $draft->last_activity_at && $draft->last_activity_at->lt(now()->subMinutes($resumeMinutes))) {
            $draft->answers()->delete();
            $draft->delete();

            return redirect()
                ->route('patient.dashboard')
                ->with('screening_expired', 'Sesi sudah kadaluwarsa. Silakan mulai skrining dari awal.');
        }

        if (! $draft) {
            $draft = Screening::create([
                'patient_id' => $id_pasien,
                'started_at' => now(),
                'last_activity_at' => now(),
            ]);
        }

        $questions = Question::orderBy('nomor')->get();

        $questionsFormatted = $questions->map(function ($q) {
            return [
                'id'       => $q->nomor,
                'teks_id'  => $q->teks_id,
                'teks_en'  => $q->teks_en,
                'subskala' => $q->subskala,
            ];
        })->values();

        $savedAnswers = Answer::where('screening_id', $draft->id)
            ->with('question')
            ->get()
            ->mapWithKeys(function ($answer) {
                return [$answer->question->nomor => $answer->nilai];
            });

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
            'value' => 'required|integer|min:0|max:3',
        ]);

        $screening = Screening::where('patient_id', $id_pasien)
            ->whereNull('selesai_at')
            ->latest()
            ->first();

        $resumeMinutes = (int) Setting::getValue('screening_resume_minutes', 30);

        if ($screening && $screening->last_activity_at && $screening->last_activity_at->lt(now()->subMinutes($resumeMinutes))) {
            $screening->answers()->delete();
            $screening->delete();

            return response()->json([
                'expired' => true,
                'message' => 'Sesi telah berakhir, mohon ulangi screening.',
                'redirect' => route('patient.dashboard'),
            ], 419);
        }

        if (! $screening) {
            $screening = Screening::create([
                'patient_id' => $id_pasien,
                'started_at' => now(),
                'last_activity_at' => now(),
            ]);
        }

        $question = Question::where('nomor', $request->question_number)->firstOrFail();

        Answer::updateOrCreate(
            [
                'screening_id' => $screening->id,
                'question_id' => $question->id,
            ],
            [
                'nilai' => $request->value,
            ]
        );

        $screening->update([
            'last_activity_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban tersimpan',
        ]);
    }

    // ── Submit jawaban → decision tree → simpan ───────────────────
    public function submit(Request $request)
    {
        $id_pasien = session('patient_id');
        if (! $id_pasien) return redirect()->route('patient.login');

        // Double-check cooldown (server-side guard)
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

        $screening = Screening::where('patient_id', $id_pasien)
            ->whereNull('selesai_at')
            ->latest()
            ->first();

        if (! $screening) {
            $screening = Screening::create([
                'patient_id' => $id_pasien,
                'started_at' => now(),
                'last_activity_at' => now(),
            ]);
        }

        $screening->update([
            'selesai_at' => now(),
            'last_activity_at' => now(),
        ]);

        $qMap = Question::orderBy('nomor')->pluck('id', 'nomor');
        foreach ($request->answers as $nomor => $nilai) {
            Answer::updateOrCreate(
                [
                    'screening_id' => $screening->id,
                    'question_id'  => $qMap[(int) $nomor],
                ],
                [
                    'nilai' => (int) $nilai,
                ]
            );
        }

        $dt    = new DecisionTreeService();
        $hasil = $dt->process(
            collect($request->answers)
                ->mapWithKeys(function ($v, $k) {
                    return [(int) $k => $v];
                })
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

        // 5 sesi terbaru untuk line chart (kronologis)
        $riwayat = Screening::where('patient_id', $screening->patient_id)
            ->whereNotNull('selesai_at')
            ->with('result')
            ->orderBy('selesai_at', 'desc')
            ->limit(5)
            ->get()
            ->reverse()
            ->values();

        $lineData = [
            'labels'    => $riwayat->map(function ($s) {
                return $s->selesai_at->format('d/m');
            })->toArray(),
            'depresi'   => $riwayat->map(function ($s) {
                return $s->result?->skor_depresi ?? 0;
            })->toArray(),
            'kecemasan' => $riwayat->map(function ($s) {
                return $s->result?->skor_kecemasan ?? 0;
            })->toArray(),
            'stres'     => $riwayat->map(function ($s) {
                return $s->result?->skor_stres ?? 0;
            })->toArray(),
        ];

        $barData = [
            'labels'     => ['Depresi', 'Kecemasan', 'Stres'],
            'scores'     => [$result->skor_depresi, $result->skor_kecemasan, $result->skor_stres],
            'categories' => [$result->kat_depresi,  $result->kat_kecemasan,  $result->kat_stres],
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
