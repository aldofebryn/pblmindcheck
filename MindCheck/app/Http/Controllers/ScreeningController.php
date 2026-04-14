<?php

namespace App\Http\Controllers;

use App\Models\{Patient, Question, Screening, Answer, Result};
use App\Services\DecisionTreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScreeningController extends Controller
{
    // ── Landing page ────────────────────────────────────────────
    public function landing()
    {
        $totalSesi = Screening::whereNotNull('selesai_at')->count();
        return view('welcome', compact('totalSesi'));
    }

    // ── Halaman pilih / input token ─────────────────────────────
    public function tokenPage()
    {
        return view('pages.token', [
            'tokenBaru' => session('token_baru'),
        ]);
    }

    // ── Proses token (generate baru atau validasi lama) ──────────
    public function processToken(Request $request)
    {
        $request->validate(['aksi' => 'required|in:baru,lama']);

        if ($request->aksi === 'baru') {
            $token = (string) Str::uuid();
            Patient::firstOrCreate(['token' => $token]);
            session(['patient_token' => $token, 'token_baru' => $token]);
        } else {
            $request->validate([
                'token' => ['required', 'string', 'regex:/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i'],
            ], ['token.regex' => 'Format token tidak valid. Pastikan token ditulis dengan benar.']);

            $patient = Patient::where('token', trim($request->token))->first();
            if (! $patient) {
                return back()
                    ->withErrors(['token' => 'Token tidak ditemukan. Periksa kembali atau buat token baru.'])
                    ->withInput();
            }
            session(['patient_token' => $patient->token]);
            session()->forget('token_baru');
        }

        return redirect()->route('token');
    }

    // ── Halaman kuesioner DASS-21 ────────────────────────────────
    public function screening()
    {
        if (! session('patient_token')) {
            return redirect()->route('token');
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

        return view('pages.screening', compact('questions', 'questionsFormatted'));
    }

    // ── Submit jawaban → jalankan decision tree → simpan ─────────
    public function submitScreening(Request $request)
    {
        $token = session('patient_token');
        if (! $token) return redirect()->route('token');

        $request->validate([
            'answers'   => 'required|array|size:21',
            'answers.*' => 'required|integer|min:0|max:3',
        ], [
            'answers.size'  => 'Semua 21 pertanyaan wajib dijawab.',
            'answers.*.min' => 'Nilai jawaban tidak valid (0–3).',
            'answers.*.max' => 'Nilai jawaban tidak valid (0–3).',
        ]);

        Patient::firstOrCreate(['token' => $token]);

        // Buat sesi skrining
        $screening = Screening::create([
            'patient_token' => $token,
            'selesai_at'    => now(),
        ]);

        // Simpan 21 jawaban
        $qMap = Question::orderBy('nomor')->pluck('id', 'nomor');
        foreach ($request->answers as $nomor => $nilai) {
            Answer::create([
                'screening_id' => $screening->id,
                'question_id'  => $qMap[(int) $nomor],
                'nilai'        => (int) $nilai,
            ]);
        }

        // Jalankan Decision Tree
        $dt    = new DecisionTreeService();
        $hasil = $dt->process(
            collect($request->answers)->mapWithKeys(fn($v, $k) => [(int)$k => $v])->toArray()
        );

        // Simpan hasil
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

    // ── Halaman hasil ────────────────────────────────────────────
    public function hasil(Screening $screening)
    {
        $result = $screening->result;
        if (! $result) abort(404);

        $dt   = new DecisionTreeService();
        $teks = DecisionTreeService::tekstRek($result->rekomendasi);

        // Riwayat sebelumnya untuk perbandingan trend
        $prev = Screening::where('patient_token', $screening->patient_token)
            ->where('id', '<', $screening->id)
            ->with('result')
            ->whereNotNull('selesai_at')
            ->orderByDesc('id')
            ->first();

        return view('pages.hasil', compact('screening', 'result', 'teks', 'prev'));
    }

    // ── Halaman riwayat pasien ───────────────────────────────────
    public function history()
    {
        $token = session('patient_token');
        if (! $token) return redirect()->route('token');

        $screenings = Screening::where('patient_token', $token)
            ->with('result')
            ->whereNotNull('selesai_at')
            ->orderByDesc('created_at')
            ->get();

        // Data untuk chart tren
        $chartData = $screenings->reverse()->values()->map(fn($s) => [
            'tanggal'    => $s->selesai_at->format('d M'),
            'depresi'    => $s->result?->skor_depresi ?? 0,
            'kecemasan'  => $s->result?->skor_kecemasan ?? 0,
            'stres'      => $s->result?->skor_stres ?? 0,
        ]);

        return view('pages.history', compact('screenings', 'token', 'chartData'));
    }
}
