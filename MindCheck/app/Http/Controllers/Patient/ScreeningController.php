<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ChecksScreeningCooldown;
use App\Models\{Screening, Question, Answer, Result};
use App\Services\DecisionTreeService;
use Illuminate\Http\Request;

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

        $questions          = Question::orderBy('nomor')->get();
        $questionsFormatted = $questions->map(fn($q) => [
            'id'       => $q->nomor,
            'teks_id'  => $q->teks_id,
            'teks_en'  => $q->teks_en,
            'subskala' => $q->subskala,
        ])->values();

        return view('patient.screening', compact('questions', 'questionsFormatted'));
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

        $screening = Screening::create([
            'patient_id' => $id_pasien,
            'selesai_at' => now(),
        ]);

        $qMap = Question::orderBy('nomor')->pluck('id', 'nomor');
        foreach ($request->answers as $nomor => $nilai) {
            Answer::create([
                'screening_id' => $screening->id,
                'question_id'  => $qMap[(int) $nomor],
                'nilai'        => (int) $nilai,
            ]);
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
            'labels'    => $riwayat->map(fn($s) => $s->selesai_at->format('d/m'))->toArray(),
            'depresi'   => $riwayat->map(fn($s) => $s->result?->skor_depresi   ?? 0)->toArray(),
            'kecemasan' => $riwayat->map(fn($s) => $s->result?->skor_kecemasan ?? 0)->toArray(),
            'stres'     => $riwayat->map(fn($s) => $s->result?->skor_stres     ?? 0)->toArray(),
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
