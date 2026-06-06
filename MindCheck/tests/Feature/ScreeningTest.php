<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Patient, Admin, Screening, Answer, Result, Question};
use App\Services\DecisionTreeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

/**
 * ============================================================
 * ScreeningTest — Automated Unit Testing
 * ============================================================
 * Menguji fungsionalitas inti sistem MindCheck:
 *
 * Grup 1 : Kontrol Akses (Access Control)
 * Grup 2 : Endpoint Autosave
 * Grup 3 : Kalkulasi Skor & Klasifikasi Decision Tree
 * Grup 4 : Submit Skrining Penuh
 * Grup 5 : Akses Admin (Dashboard & Proteksi Rute)
 * Grup 6 : Boundary Value Analysis — 20 Skenario BVA
 * ============================================================
 */
class ScreeningTest extends TestCase
{
    use RefreshDatabase;

    // ── DASS-21 subscale mapping ──────────────────────────────────
    private const SUBSCALE_MAP = [
        'depresi'   => [3, 5, 10, 13, 16, 17, 21],
        'kecemasan' => [2, 4,  7,  9, 15, 19, 20],
        'stres'     => [1, 6,  8, 11, 12, 14, 18],
    ];

    // ─────────────────────────────────────────────────────────────
    // SETUP — Buat patient & admin yang dipakai bersama
    // ─────────────────────────────────────────────────────────────

    /** Buat patient dan seed 21 questions, kembalikan patient */
    private function makePatient(array $overrides = []): Patient
    {
        $this->seedQuestions();
        return Patient::create(array_merge([
            'alias'            => 'Pasien Uji',
            'username'         => 'pasienuji',
            'password'         => Hash::make('password123'),
            'umur'             => 22,
            'status_pekerjaan' => 'Pelajar/Mahasiswa',
        ], $overrides));
    }

    /** Buat admin */
    private function makeAdmin(): Admin
    {
        return Admin::create([
            'name'     => 'Admin Uji',
            'email'    => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'status'   => true,
        ]);
    }

    /** Seed 21 pertanyaan DASS-21 jika belum ada */
    private function seedQuestions(): void
    {
        if (Question::count() === 0) {
            // Nilai enum harus sesuai CHECK constraint: 'Depression', 'Anxiety', 'Stress'
            $subscales = [
                1  => 'Stress',     2  => 'Anxiety',    3  => 'Depression', 4  => 'Anxiety',
                5  => 'Depression', 6  => 'Stress',     7  => 'Anxiety',    8  => 'Stress',
                9  => 'Anxiety',    10 => 'Depression', 11 => 'Stress',     12 => 'Stress',
                13 => 'Depression', 14 => 'Stress',     15 => 'Anxiety',    16 => 'Depression',
                17 => 'Depression', 18 => 'Stress',     19 => 'Anxiety',    20 => 'Anxiety',
                21 => 'Depression',
            ];
            foreach ($subscales as $nomor => $sub) {
                Question::create([
                    'nomor'    => $nomor,
                    'teks_id'  => "Pertanyaan {$nomor} (ID)",
                    'teks_en'  => "Question {$nomor} (EN)",
                    'subskala' => $sub,
                ]);
            }
        }
    }

    /** Simulasi login pasien via session */
    private function loginPatient(Patient $patient): void
    {
        Session::put('patient_id', $patient->id);
    }

    /** Simulasi login admin via session */
    private function loginAdmin(Admin $admin): void
    {
        Session::put('admin_id', $admin->id);
        Session::put('admin_name', $admin->name);
    }

    /** Bangun array jawaban dari skor target per subskala */
    private function buildAnswers(int $rawD, int $rawA, int $rawS): array
    {
        $answers = [];
        foreach (self::SUBSCALE_MAP['depresi']   as $no) $answers[$no] = 0;
        foreach (self::SUBSCALE_MAP['kecemasan'] as $no) $answers[$no] = 0;
        foreach (self::SUBSCALE_MAP['stres']     as $no) $answers[$no] = 0;

        $this->fillSubscale(self::SUBSCALE_MAP['depresi'],   $rawD, $answers);
        $this->fillSubscale(self::SUBSCALE_MAP['kecemasan'], $rawA, $answers);
        $this->fillSubscale(self::SUBSCALE_MAP['stres'],     $rawS, $answers);

        return $answers;
    }

    private function fillSubscale(array $nos, int $target, array &$answers): void
    {
        $rem = min($target, count($nos) * 3);
        foreach ($nos as $no) {
            if ($rem <= 0) break;
            $give = min(3, $rem);
            $answers[$no] = $give;
            $rem -= $give;
        }
    }

    // =============================================================
    // GRUP 1 — Kontrol Akses
    // =============================================================

    /** T01 — Halaman skrining redirect ke login jika tidak ada sesi */
    public function test_T01_screening_page_redirects_unauthenticated_patient(): void
    {
        $response = $this->get('/screening');
        $response->assertRedirect('/patient-login');
    }

    /** T02 — Halaman dashboard redirect ke login jika tidak ada sesi */
    public function test_T02_patient_dashboard_redirects_unauthenticated(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/patient-login');
    }

    /** T03 — Pasien yang login berhasil melihat halaman skrining */
    public function test_T03_authenticated_patient_can_access_screening(): void
    {
        $patient = $this->makePatient();
        $this->loginPatient($patient);

        $response = $this->get('/screening');
        $response->assertStatus(200);
        $response->assertViewIs('patient.screening');
    }

    /** T04 — Admin dashboard redirect jika belum login */
    public function test_T04_admin_dashboard_redirects_unauthenticated(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect();
    }

    /** T05 — Admin yang login berhasil melihat dashboard */
    public function test_T05_authenticated_admin_can_access_dashboard(): void
    {
        $admin = $this->makeAdmin();
        $this->loginAdmin($admin);

        $response = $this->get('/admin');
        $response->assertStatus(200);
    }

    // =============================================================
    // GRUP 2 — Endpoint Autosave
    // =============================================================

    /** T06 — Autosave tanpa sesi mengembalikan 401 */
    public function test_T06_autosave_returns_401_without_session(): void
    {
        $response = $this->postJson('/screening/autosave', [
            'question_number' => 1,
            'value'           => 2,
        ]);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized']);
    }

    /** T07 — Autosave berhasil menyimpan jawaban (200 + success=true) */
    public function test_T07_autosave_saves_answer_successfully(): void
    {
        $patient = $this->makePatient();
        $this->loginPatient($patient);

        // Buat draft screening terlebih dahulu
        Screening::create([
            'patient_id'       => $patient->id,
            'started_at'       => now(),
            'last_activity_at' => now(),
        ]);

        $response = $this->postJson('/screening/autosave', [
            'question_number' => 3,
            'value'           => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('answers', ['nilai' => 2]);
    }

    /** T08 — Autosave menolak question_number di luar range 1-21 */
    public function test_T08_autosave_rejects_invalid_question_number(): void
    {
        $patient = $this->makePatient();
        $this->loginPatient($patient);

        $response = $this->postJson('/screening/autosave', [
            'question_number' => 99,
            'value'           => 1,
        ]);

        $response->assertStatus(422); // Validation error
    }

    /** T09 — Autosave menolak nilai jawaban di luar range 0-3 */
    public function test_T09_autosave_rejects_value_out_of_range(): void
    {
        $patient = $this->makePatient();
        $this->loginPatient($patient);

        $response = $this->postJson('/screening/autosave', [
            'question_number' => 1,
            'value'           => 5,
        ]);

        $response->assertStatus(422);
    }

    /** T10 — Autosave memperbarui jawaban yang sudah ada (updateOrCreate) */
    public function test_T10_autosave_updates_existing_answer(): void
    {
        $patient  = $this->makePatient();
        $this->loginPatient($patient);

        $screening = Screening::create([
            'patient_id'       => $patient->id,
            'started_at'       => now(),
            'last_activity_at' => now(),
        ]);

        $question = Question::where('nomor', 1)->first();

        Answer::create([
            'screening_id' => $screening->id,
            'question_id'  => $question->id,
            'nilai'        => 0,
        ]);

        // Update jawaban dari 0 ke 3
        $this->postJson('/screening/autosave', ['question_number' => 1, 'value' => 3]);

        $this->assertDatabaseHas('answers', [
            'screening_id' => $screening->id,
            'question_id'  => $question->id,
            'nilai'        => 3,
        ]);
        $this->assertDatabaseCount('answers', 1); // Tidak duplikat
    }

    // =============================================================
    // GRUP 3 — Kalkulasi Skor & Decision Tree
    // =============================================================

    /** T11 — Skor 0 semua = Normal, Normal, Normal → R18 */
    public function test_T11_decision_tree_all_zero_scores(): void
    {
        $dt     = new DecisionTreeService();
        $result = $dt->process(array_fill_keys(range(1, 21), 0));

        $this->assertEquals(0, $result['skor_d']);
        $this->assertEquals(0, $result['skor_a']);
        $this->assertEquals(0, $result['skor_s']);
        $this->assertEquals('Normal', $result['kat_d']);
        $this->assertEquals('Normal', $result['kat_a']);
        $this->assertEquals('Normal', $result['kat_s']);
        $this->assertEquals('R18', $result['rekomendasi']);
    }

    /** T12 — Konversi ×2 benar: rawD=5 → skor_d=10 */
    public function test_T12_raw_score_is_multiplied_by_two(): void
    {
        $dt      = new DecisionTreeService();
        $answers = $this->buildAnswers(5, 0, 0);
        $result  = $dt->process($answers);

        $this->assertEquals(10, $result['skor_d']); // 5 × 2 = 10
        $this->assertEquals(0,  $result['skor_a']);
        $this->assertEquals(0,  $result['skor_s']);
    }

    /** T13 — Threshold Depresi: rawD=7 → skor=14 → Sedang */
    public function test_T13_depression_threshold_moderate(): void
    {
        $dt     = new DecisionTreeService();
        $result = $dt->process($this->buildAnswers(7, 0, 0));

        $this->assertEquals(14, $result['skor_d']);
        $this->assertEquals('Sedang', $result['kat_d']);
    }

    /** T14 — Threshold Kecemasan: rawA=8 → skor=16 → Berat */
    public function test_T14_anxiety_threshold_severe(): void
    {
        $dt     = new DecisionTreeService();
        $result = $dt->process($this->buildAnswers(0, 8, 0));

        $this->assertEquals(16, $result['skor_a']);
        $this->assertEquals('Berat', $result['kat_a']);
    }

    /** T15 — Threshold Stres: rawS=17 → skor=34 → Sangat Berat */
    public function test_T15_stress_threshold_extremely_severe(): void
    {
        $dt     = new DecisionTreeService();
        $result = $dt->process($this->buildAnswers(0, 0, 17));

        $this->assertEquals(34, $result['skor_s']);
        $this->assertEquals('Sangat Berat', $result['kat_s']);
    }

    /** T16 — Rekomendasi R16 dipicu oleh satu subskala Berat */
    public function test_T16_recommendation_R16_triggered_by_one_severe(): void
    {
        $dt     = new DecisionTreeService();
        $result = $dt->process($this->buildAnswers(11, 0, 0)); // rawD=11 → Berat

        $this->assertEquals('Berat', $result['kat_d']);
        $this->assertEquals('R16', $result['rekomendasi']);
    }

    /** T17 — Rekomendasi R17 dipicu oleh satu subskala Sedang */
    public function test_T17_recommendation_R17_triggered_by_one_moderate(): void
    {
        $dt     = new DecisionTreeService();
        $result = $dt->process($this->buildAnswers(7, 0, 0)); // rawD=7 → Sedang

        $this->assertEquals('Sedang', $result['kat_d']);
        $this->assertEquals('R17', $result['rekomendasi']);
    }

    /** T18 — Skor maksimum 21 semua → 42,42,42 → Sangat Berat semua → R16 */
    public function test_T18_maximum_scores_all_extremely_severe(): void
    {
        $dt     = new DecisionTreeService();
        $result = $dt->process(array_fill_keys(range(1, 21), 3));

        $this->assertEquals(42, $result['skor_d']);
        $this->assertEquals(42, $result['skor_a']);
        $this->assertEquals(42, $result['skor_s']);
        $this->assertEquals('Sangat Berat', $result['kat_d']);
        $this->assertEquals('Sangat Berat', $result['kat_a']);
        $this->assertEquals('Sangat Berat', $result['kat_s']);
        $this->assertEquals('R16', $result['rekomendasi']);
    }

    // =============================================================
    // GRUP 4 — Submit Skrining Penuh
    // =============================================================

    /** T19 — Submit 21 jawaban valid → redirect ke halaman hasil */
    public function test_T19_submit_valid_answers_redirects_to_hasil(): void
    {
        $patient = $this->makePatient();
        $this->loginPatient($patient);

        $answers = array_fill_keys(range(1, 21), 1);

        $response = $this->post('/screening', [
            '_token'  => csrf_token(),
            'answers' => $answers,
        ]);

        $response->assertRedirectContains('/hasil/');
        $this->assertDatabaseCount('screenings', 1);
        $this->assertDatabaseCount('results', 1);
    }

    /** T20 — Submit kurang dari 21 jawaban → gagal validasi */
    public function test_T20_submit_incomplete_answers_fails_validation(): void
    {
        $patient = $this->makePatient();
        $this->loginPatient($patient);

        // Hanya 10 jawaban dari 21 yang diperlukan
        $answers = array_fill_keys(range(1, 10), 1);

        $response = $this->post('/screening', [
            '_token'  => csrf_token(),
            'answers' => $answers,
        ]);

        $response->assertSessionHasErrors('answers');
    }

    /** T21 — Submit menyimpan hasil ke tabel results */
    public function test_T21_submit_creates_result_record(): void
    {
        $patient = $this->makePatient();
        $this->loginPatient($patient);

        $answers = $this->buildAnswers(7, 5, 10); // Sedang, Sedang, Sedang → R17

        $this->post('/screening', [
            '_token'  => csrf_token(),
            'answers' => $answers,
        ]);

        $this->assertDatabaseCount('results', 1);

        $result = Result::first();
        $this->assertEquals('Sedang', $result->kat_depresi);
        $this->assertEquals('Sedang', $result->kat_kecemasan);
        $this->assertEquals('Sedang', $result->kat_stres);
        $this->assertEquals('R17', $result->rekomendasi);
    }

    // =============================================================
    // GRUP 5 — Akses Admin
    // =============================================================

    /** T22 — Admin dapat mengakses daftar pasien */
    public function test_T22_admin_can_view_patients_list(): void
    {
        $admin = $this->makeAdmin();
        $this->loginAdmin($admin);

        $response = $this->get('/admin/patients');
        $response->assertStatus(200);
    }

    /** T23 — Admin dapat mengakses halaman informasi sistem */
    public function test_T23_admin_can_view_info_page(): void
    {
        $admin = $this->makeAdmin();
        $this->loginAdmin($admin);

        $response = $this->get('/admin/info');
        $response->assertStatus(200);
        $response->assertSeeText('MindCheck');
    }

    /** T24 — Rute admin/patients tidak bisa diakses tanpa login */
    public function test_T24_admin_patients_blocked_without_auth(): void
    {
        $response = $this->get('/admin/patients');
        $response->assertRedirect();
        $response->assertStatus(302);
    }

    // =============================================================
    // GRUP 6 — Boundary Value Analysis (20 Skenario)
    // =============================================================

    /**
     * Data provider BVA: [id, rawD, rawA, rawS, katD, katA, katS, rek]
     */
    public static function bvaProvider(): array
    {
        return [
            'UJ-01' => [ 0,  0,  0, 'Normal',      'Normal',      'Normal',      'R18'],
            'UJ-02' => [ 4,  3,  7, 'Normal',      'Normal',      'Normal',      'R18'],
            'UJ-03' => [ 6,  4,  9, 'Ringan',      'Ringan',      'Ringan',      'R18'],
            'UJ-04' => [ 7,  5, 10, 'Sedang',      'Sedang',      'Sedang',      'R17'],
            'UJ-05' => [ 4,  4, 11, 'Normal',      'Ringan',      'Sedang',      'R17'],
            'UJ-06' => [11,  8, 13, 'Berat',       'Berat',       'Berat',       'R16'],
            'UJ-07' => [11,  2,  5, 'Berat',       'Normal',      'Normal',      'R16'],
            'UJ-08' => [15, 11, 18, 'Sangat Berat','Sangat Berat','Sangat Berat','R16'],
            'UJ-09' => [21, 21, 21, 'Sangat Berat','Sangat Berat','Sangat Berat','R16'],
            'UJ-10' => [ 8,  2,  5, 'Sedang',      'Normal',      'Normal',      'R17'],
            'UJ-11' => [ 2,  6,  4, 'Normal',      'Sedang',      'Normal',      'R17'],
            'UJ-12' => [ 3,  1, 11, 'Normal',      'Normal',      'Sedang',      'R17'],
            'UJ-13' => [12,  4,  8, 'Berat',       'Ringan',      'Ringan',      'R16'],
            'UJ-14' => [ 8,  8, 11, 'Sedang',      'Berat',       'Sedang',      'R16'],
            'UJ-15' => [ 5,  4, 14, 'Ringan',      'Ringan',      'Berat',       'R16'],
            'UJ-16' => [15,  2,  6, 'Sangat Berat','Normal',      'Normal',      'R16'],
            'UJ-17' => [ 4, 11,  5, 'Normal',      'Sangat Berat','Normal',      'R16'],
            'UJ-18' => [ 2,  1, 18, 'Normal',      'Normal',      'Sangat Berat','R16'],
            'UJ-19' => [10,  7, 12, 'Sedang',      'Sedang',      'Sedang',      'R17'],
            'UJ-20' => [ 9,  6,  9, 'Sedang',      'Sedang',      'Ringan',      'R17'],
        ];
    }

    /**
     * T25 — BVA: Semua 20 skenario pengujian batas nilai
     *
     * @dataProvider bvaProvider
     */
    public function test_T25_bva_classification(
        int $rawD, int $rawA, int $rawS,
        string $expKatD, string $expKatA, string $expKatS, string $expRek
    ): void {
        $dt      = new DecisionTreeService();
        $answers = $this->buildAnswers($rawD, $rawA, $rawS);
        $result  = $dt->process($answers);

        $this->assertEquals($rawD * 2, $result['skor_d'], "Konversi skor Depresi ×2 gagal");
        $this->assertEquals($rawA * 2, $result['skor_a'], "Konversi skor Kecemasan ×2 gagal");
        $this->assertEquals($rawS * 2, $result['skor_s'], "Konversi skor Stres ×2 gagal");
        $this->assertEquals($expKatD,  $result['kat_d'],  "Kategori Depresi tidak sesuai");
        $this->assertEquals($expKatA,  $result['kat_a'],  "Kategori Kecemasan tidak sesuai");
        $this->assertEquals($expKatS,  $result['kat_s'],  "Kategori Stres tidak sesuai");
        $this->assertEquals($expRek,   $result['rekomendasi'], "Kode rekomendasi tidak sesuai");
    }
}
