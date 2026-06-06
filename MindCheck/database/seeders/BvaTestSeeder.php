<?php

namespace Database\Seeders;

use App\Models\{Patient, Screening, Answer, Result, Question};
use App\Services\DecisionTreeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * ============================================================
 * BVA Test Seeder — Boundary Value Analysis
 * ============================================================
 * Membuat 20 akun pasien uji yang masing-masing mewakili satu
 * skenario pengujian (test case) berdasarkan metode Boundary
 * Value Analysis (BVA) terhadap algoritma klasifikasi DASS-21.
 *
 * Setiap pasien memiliki tepat SATU sesi skrining dengan skor
 * yang ditentukan secara presisi sesuai tabel uji.
 *
 * Semua skor dihitung melalui DecisionTreeService yang sama
 * dengan yang digunakan sistem produksi untuk menjamin
 * konsistensi hasil.
 *
 * Password semua akun: test123456
 * ============================================================
 */
class BvaTestSeeder extends Seeder
{
    /**
     * 20 Test Cases — Boundary Value Analysis DASS-21
     *
     * Format: [id_uji, rawD, rawA, rawS, ekspektasi_kat_d, ekspektasi_kat_a, ekspektasi_kat_s, ekspektasi_rek, keterangan]
     */
    private const TEST_CASES = [
        // ID     D   A   S   Kat-D          Kat-A          Kat-S          Rek    Keterangan
        ['UJ-01',  0,  0,  0, 'Normal',      'Normal',      'Normal',      'R18', 'Batas paling bawah (Semua jawaban bernilai 0)'],
        ['UJ-02',  4,  3,  7, 'Normal',      'Normal',      'Normal',      'R18', 'Batas atas maksimal untuk kategori Normal'],
        ['UJ-03',  6,  4,  9, 'Ringan',      'Ringan',      'Ringan',      'R18', 'Batas maksimal untuk kategori Ringan'],
        ['UJ-04',  7,  5, 10, 'Sedang',      'Sedang',      'Sedang',      'R17', 'Angka batas paling bawah (threshold) kategori Sedang'],
        ['UJ-05',  4,  4, 11, 'Normal',      'Ringan',      'Sedang',      'R17', 'Kombinasi acak yang memicu R17 (ada Sedang)'],
        ['UJ-06', 11,  8, 13, 'Berat',       'Berat',       'Berat',       'R16', 'Angka batas transisi masuk ke kategori Berat'],
        ['UJ-07', 11,  2,  5, 'Berat',       'Normal',      'Normal',      'R16', 'Satu saja yang Berat (Depresi), wajib R16'],
        ['UJ-08', 15, 11, 18, 'Sangat Berat','Sangat Berat','Sangat Berat','R16', 'Kasus ekstrem atas di semua indikator'],
        ['UJ-09', 21, 21, 21, 'Sangat Berat','Sangat Berat','Sangat Berat','R16', 'Kasus mutlak tertinggi (Semua 21 soal dijawab poin 3)'],
        ['UJ-10',  8,  2,  5, 'Sedang',      'Normal',      'Normal',      'R17', 'Satu saja yang Sedang (Depresi), wajib R17'],
        ['UJ-11',  2,  6,  4, 'Normal',      'Sedang',      'Normal',      'R17', 'Kecemasan pada level Sedang'],
        ['UJ-12',  3,  1, 11, 'Normal',      'Normal',      'Sedang',      'R17', 'Stres pada level Sedang'],
        ['UJ-13', 12,  4,  8, 'Berat',       'Ringan',      'Ringan',      'R16', 'Memastikan Berat meng-override Ringan'],
        ['UJ-14',  8,  8, 11, 'Sedang',      'Berat',       'Sedang',      'R16', 'Memastikan kategori Berat pada Kecemasan terdeteksi'],
        ['UJ-15',  5,  4, 14, 'Ringan',      'Ringan',      'Berat',       'R16', 'Memastikan kategori Berat pada Stres terdeteksi'],
        ['UJ-16', 15,  2,  6, 'Sangat Berat','Normal',      'Normal',      'R16', 'Uji deteksi Sangat Berat spesifik pada Depresi'],
        ['UJ-17',  4, 11,  5, 'Normal',      'Sangat Berat','Normal',      'R16', 'Uji deteksi Sangat Berat spesifik pada Kecemasan'],
        ['UJ-18',  2,  1, 18, 'Normal',      'Normal',      'Sangat Berat','R16', 'Uji deteksi Sangat Berat spesifik pada Stres'],
        ['UJ-19', 10,  7, 12, 'Sedang',      'Sedang',      'Sedang',      'R17', 'Batas maksimal tertinggi kategori Sedang sebelum Berat'],
        ['UJ-20',  9,  6,  9, 'Sedang',      'Sedang',      'Ringan',      'R17', 'Kasus kombinasi realistis pada pasien umum'],
    ];

    // DASS-21 subscale question number mapping
    private const SUBSCALE_MAP = [
        'depresi'   => [3, 5, 10, 13, 16, 17, 21],
        'kecemasan' => [2, 4,  7,  9, 15, 19, 20],
        'stres'     => [1, 6,  8, 11, 12, 14, 18],
    ];

    public function run(): void
    {
        // Fetch all questions
        $questions = Question::all();
        if ($questions->isEmpty()) {
            $this->command->error('Tabel questions kosong! Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // Map: nomor soal (1-21) => database question ID
        $questionIdMap = $questions->pluck('id', 'nomor')->toArray();

        $dtService      = new DecisionTreeService();
        $hashedPassword = Hash::make('test123456');
        $baseDate       = Carbon::parse('2026-05-01 09:00:00');

        $this->command->info('Memulai seeding 20 test case BVA...');
        $this->command->newLine();

        DB::beginTransaction();

        try {
            $passCount = 0;
            $failCount = 0;

            foreach (self::TEST_CASES as $idx => [$idUji, $rawD, $rawA, $rawS, $expKatD, $expKatA, $expKatS, $expRek, $keterangan]) {

                // ── 1. Buat akun pasien uji ───────────────────
                $username = 'uji' . strtolower(str_replace('-', '', $idUji)); // e.g. "ujibva01"
                $patient  = Patient::create([
                    'alias'            => 'Test ' . $idUji,
                    'username'         => $username,
                    'password'         => $hashedPassword,
                    'umur'             => 22,
                    'status_pekerjaan' => 'Pelajar/Mahasiswa',
                    'created_at'       => $baseDate,
                    'updated_at'       => $baseDate,
                ]);

                // ── 2. Bangun array jawaban dari skor mentah ──
                //    Distribusikan skor per subskala ke 7 soal masing-masing
                $answers = [];
                $answers = $this->distributeExact(self::SUBSCALE_MAP['depresi'],   $rawD, $answers);
                $answers = $this->distributeExact(self::SUBSCALE_MAP['kecemasan'], $rawA, $answers);
                $answers = $this->distributeExact(self::SUBSCALE_MAP['stres'],     $rawS, $answers);

                // ── 3. Proses melalui DecisionTreeService ─────
                $processed = $dtService->process($answers);

                // ── 4. Verifikasi hasil vs ekspektasi ─────────
                $actualKatD = $processed['kat_d'];
                $actualKatA = $processed['kat_a'];
                $actualKatS = $processed['kat_s'];
                $actualRek  = $processed['rekomendasi'];
                $actualSkorD = $processed['skor_d']; // rawD * 2
                $actualSkorA = $processed['skor_a'];
                $actualSkorS = $processed['skor_s'];

                $pass = ($actualKatD === $expKatD)
                     && ($actualKatA === $expKatA)
                     && ($actualKatS === $expKatS)
                     && ($actualRek  === $expRek);

                if ($pass) {
                    $passCount++;
                    $this->command->line(
                        " <fg=green>✓ PASS</> {$idUji} | D:{$actualSkorD}({$actualKatD}) A:{$actualSkorA}({$actualKatA}) S:{$actualSkorS}({$actualKatS}) → {$actualRek}"
                    );
                } else {
                    $failCount++;
                    $this->command->line(
                        " <fg=red>✗ FAIL</> {$idUji} | Got: D:{$actualKatD} A:{$actualKatA} S:{$actualKatS} → {$actualRek} | Exp: D:{$expKatD} A:{$expKatA} S:{$expKatS} → {$expRek}"
                    );
                }

                // ── 5. Simpan Screening ───────────────────────
                $screeningTime = $baseDate->copy()->addDays($idx);
                $screening = Screening::create([
                    'patient_id'       => $patient->id,
                    'started_at'       => $screeningTime->copy()->subMinutes(5),
                    'last_activity_at' => $screeningTime,
                    'selesai_at'       => $screeningTime,
                    'created_at'       => $screeningTime,
                    'updated_at'       => $screeningTime,
                ]);

                // ── 6. Simpan Answers ─────────────────────────
                $answersData = [];
                foreach ($answers as $nomor => $nilai) {
                    $answersData[] = [
                        'screening_id' => $screening->id,
                        'question_id'  => $questionIdMap[$nomor],
                        'nilai'        => $nilai,
                        'created_at'   => $screeningTime,
                        'updated_at'   => $screeningTime,
                    ];
                }
                DB::table('answers')->insert($answersData);

                // ── 7. Simpan Result ──────────────────────────
                Result::create([
                    'screening_id'   => $screening->id,
                    'skor_depresi'   => $processed['skor_d'],
                    'skor_kecemasan' => $processed['skor_a'],
                    'skor_stres'     => $processed['skor_s'],
                    'kat_depresi'    => $processed['kat_d'],
                    'kat_kecemasan'  => $processed['kat_a'],
                    'kat_stres'      => $processed['kat_s'],
                    'rekomendasi'    => $processed['rekomendasi'],
                    'created_at'     => $screeningTime,
                    'updated_at'     => $screeningTime,
                ]);
            }

            DB::commit();

            $this->command->newLine();
            $this->command->line('─────────────────────────────────────────');
            $this->command->info("  Hasil Verifikasi BVA:");
            $this->command->line("  ✓ PASS : {$passCount} / 20 test case");
            if ($failCount > 0) {
                $this->command->error("  ✗ FAIL : {$failCount} / 20 test case");
            } else {
                $this->command->info("  ✗ FAIL : 0 / 20 test case");
                $this->command->info('  🎉 Semua test case lulus! Sistem klasifikasi valid.');
            }
            $this->command->line('─────────────────────────────────────────');
            $this->command->newLine();
            $this->command->info('Password akun uji: test123456');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error saat seeding BVA: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Distribusikan skor mentah secara deterministik ke array jawaban.
     * Mengisi setiap soal secara berurutan hingga skor terpenuhi.
     * Metode deterministik (bukan random) agar hasil selalu konsisten.
     */
    private function distributeExact(array $questionNumbers, int $targetScore, array $existing = []): array
    {
        $answers  = $existing;
        $target   = min($targetScore, count($questionNumbers) * 3);
        $remaining = $target;

        // Inisialisasi semua soal ke 0
        foreach ($questionNumbers as $no) {
            $answers[$no] = 0;
        }

        // Isi nilai 0-3 secara berurutan hingga skor terpenuhi
        foreach ($questionNumbers as $no) {
            if ($remaining <= 0) break;
            $give = min(3, $remaining);
            $answers[$no] = $give;
            $remaining   -= $give;
        }

        return $answers;
    }
}
