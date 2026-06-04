<?php

namespace Database\Seeders;

use App\Models\{Patient, Screening, Answer, Result, Question};
use App\Services\DecisionTreeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientScreeningSeeder extends Seeder
{
    public function run(): void
    {
        // 50 Realistic Indonesian names
        $names = [
            'Budi Santoso', 'Siti Aminah', 'Dian Prasetyo', 'Sri Wahyuni', 'Ahmad Hidayat',
            'Indah Permatasari', 'Muhammad Rizky', 'Dewi Lestari', 'Agung Wibowo', 'Rini Astuti',
            'Hendra Wijaya', 'Lani Marlina', 'Bambang Hermawan', 'Eka Putri', 'Adi Nugroho',
            'Yanti Susanti', 'Tri Cahyono', 'Mega Utami', 'Joko Susilo', 'Ani Wijayanti',
            'Surya Saputra', 'Nengsih Sri', 'Doni Damara', 'Fitriani Lestari', 'Anton Subagyo',
            'Maria Ulfah', 'Slamet Riyadi', 'Wulan Sari', 'Agus Setiawan', 'Ratna Juwita',
            'Gede Putra', 'Putu Ayu', 'Nyoman Satria', 'Made Devi', 'Ketut Krisna',
            'Wayan Sudarta', 'Asep Sunandar', 'Cecep Mulyana', 'Dedi Kusnandar', 'Eneng Rohayah',
            'Ujang Hermawan', 'Tatang Rustandi', 'Cucu Sumiati', 'Nunung Nurhayati', 'Iwan Setiawan',
            'Ridwan Hakim', 'Fajar Ramadhan', 'Panji Gumilang', 'Anissa Rahma', 'Megawati Putri'
        ];

        $occupations = [
            'Pelajar/Mahasiswa', 'Bekerja', 'Tidak Bekerja', 'Lainnya'
        ];

        // DASS-21 Question categories definition (same as in DecisionTreeService)
        $subscaleMap = [
            'depresi'   => [3, 5, 10, 13, 16, 17, 21],
            'kecemasan' => [2, 4, 7, 9, 15, 19, 20],
            'stres'     => [1, 6, 8, 11, 12, 14, 18],
        ];

        // Fetch questions from database to map question numbers to database IDs
        $questions = Question::all();
        if ($questions->isEmpty()) {
            $this->command->error('Tabel questions kosong! Silakan jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // Map: question_number (1-21) => question_database_id
        $questionIdMap = $questions->pluck('id', 'nomor')->toArray();

        // Target raw score ranges per severity category
        $rawScoreRanges = [
            'depresi' => [
                'Normal'       => [0, 4],
                'Ringan'       => [5, 6],
                'Sedang'       => [7, 10],
                'Berat'        => [11, 13],
                'Sangat Berat' => [14, 21],
            ],
            'kecemasan' => [
                'Normal'       => [0, 3],
                'Ringan'       => [4, 4],
                'Sedang'       => [5, 7],
                'Berat'        => [8, 9],
                'Sangat Berat' => [10, 21],
            ],
            'stres' => [
                'Normal'       => [0, 7],
                'Ringan'       => [8, 9],
                'Sedang'       => [10, 12],
                'Berat'        => [13, 16],
                'Sangat Berat' => [17, 21],
            ],
        ];

        $dtService = new DecisionTreeService();
        $hashedPassword = Hash::make('123456');
        $endDate = Carbon::parse('2026-06-01');

        $this->command->info('Memulai seeding data dummy 50 pasien dan screening...');

        // Start transaction for database safety and speed
        DB::beginTransaction();

        try {
            foreach ($names as $idx => $name) {
                // Generate unique username
                $baseUsername = strtolower(str_replace(' ', '', $name));
                $username = $baseUsername;
                $counter = 1;
                while (Patient::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter++;
                }

                $umur = rand(18, 48);
                $pekerjaan = $occupations[array_rand($occupations)];

                // Registration date is randomly spread between April 1, 2026 and May 15, 2026
                $regDate = Carbon::parse('2026-04-01')->addDays(rand(0, 44));

                // Create Patient
                $patient = Patient::create([
                    'alias'            => $name,
                    'username'         => $username,
                    'password'         => $hashedPassword,
                    'umur'             => $umur,
                    'status_pekerjaan' => $pekerjaan,
                    'created_at'       => $regDate,
                    'updated_at'       => $regDate,
                ]);

                // Determine profile type
                // 0, 1, 2, 3, 4, 5 => Healthy (60%)
                // 6, 7             => Improving (20%)
                // 8                => Worsening (10%)
                // 9                => Fluctuating (10%)
                $profileType = $idx % 10;

                // Loop weekly from registration date to end date
                $currentDate = $regDate->copy()->addDays(rand(0, 3)); // Random day offset for first screening
                $totalWeeks = ceil($currentDate->diffInWeeks($endDate));
                $currentWeek = 1;

                while ($currentDate->lte($endDate)) {
                    // Set realistic time of day (between 07:00 and 22:00)
                    $currentDate->setHour(rand(7, 21))->setMinute(rand(0, 59))->setSecond(rand(0, 59));

                    // 1. Determine severity target per subscale based on profile
                    $targets = ['depresi' => 'Normal', 'kecemasan' => 'Normal', 'stres' => 'Normal'];

                    if ($profileType < 6) {
                        // Healthy
                        $targets['depresi']   = rand(1, 10) == 10 ? 'Ringan' : 'Normal';
                        $targets['kecemasan'] = rand(1, 10) == 10 ? 'Ringan' : 'Normal';
                        $targets['stres']     = rand(1, 20) == 20 ? 'Ringan' : 'Normal';
                    } elseif ($profileType === 6 || $profileType === 7) {
                        // Improving (Severe -> Mild -> Normal)
                        $progress = $currentWeek / max($totalWeeks, 1);
                        if ($progress < 0.35) {
                            $targets['depresi']   = ['Sedang', 'Berat', 'Sangat Berat'][rand(0, 2)];
                            $targets['kecemasan'] = ['Sedang', 'Berat', 'Sangat Berat'][rand(0, 2)];
                            $targets['stres']     = ['Sedang', 'Berat', 'Sangat Berat'][rand(0, 2)];
                        } elseif ($progress < 0.7) {
                            $targets['depresi']   = ['Ringan', 'Sedang'][rand(0, 1)];
                            $targets['kecemasan'] = ['Ringan', 'Sedang'][rand(0, 1)];
                            $targets['stres']     = ['Ringan', 'Sedang'][rand(0, 1)];
                        } else {
                            $targets['depresi']   = rand(1, 5) == 5 ? 'Ringan' : 'Normal';
                            $targets['kecemasan'] = rand(1, 5) == 5 ? 'Ringan' : 'Normal';
                            $targets['stres']     = rand(1, 5) == 5 ? 'Ringan' : 'Normal';
                        }
                    } elseif ($profileType === 8) {
                        // Worsening (Normal -> Mild -> Severe)
                        $progress = $currentWeek / max($totalWeeks, 1);
                        if ($progress < 0.35) {
                            $targets['depresi']   = rand(1, 5) == 5 ? 'Ringan' : 'Normal';
                            $targets['kecemasan'] = rand(1, 5) == 5 ? 'Ringan' : 'Normal';
                            $targets['stres']     = rand(1, 5) == 5 ? 'Ringan' : 'Normal';
                        } elseif ($progress < 0.7) {
                            $targets['depresi']   = ['Ringan', 'Sedang'][rand(0, 1)];
                            $targets['kecemasan'] = ['Ringan', 'Sedang'][rand(0, 1)];
                            $targets['stres']     = ['Ringan', 'Sedang'][rand(0, 1)];
                        } else {
                            $targets['depresi']   = ['Sedang', 'Berat', 'Sangat Berat'][rand(0, 2)];
                            $targets['kecemasan'] = ['Sedang', 'Berat', 'Sangat Berat'][rand(0, 2)];
                            $targets['stres']     = ['Sedang', 'Berat', 'Sangat Berat'][rand(0, 2)];
                        }
                    } else {
                        // Fluctuating
                        $cats = ['Normal', 'Ringan', 'Sedang', 'Berat', 'Sangat Berat'];
                        $weights = [0.2, 0.3, 0.3, 0.15, 0.05];
                        
                        foreach (['depresi', 'kecemasan', 'stres'] as $sub) {
                            $randVal = rand(1, 100) / 100;
                            $sum = 0;
                            $chosen = 'Normal';
                            foreach ($cats as $cIdx => $cat) {
                                $sum += $weights[$cIdx];
                                if ($randVal <= $sum) {
                                    $chosen = $cat;
                                    break;
                                }
                            }
                            $targets[$sub] = $chosen;
                        }
                    }

                    // 2. Generate target scores and answer numbers mapping
                    $answers = []; // [nomor => nilai 0-3]
                    foreach ($subscaleMap as $sub => $questionNumbers) {
                        $range = $rawScoreRanges[$sub][$targets[$sub]];
                        $targetRawScore = rand($range[0], $range[1]);

                        // Distribute target raw score to 7 questions
                        $subAnswers = $this->distributeRawScore($questionNumbers, $targetRawScore);
                        foreach ($subAnswers as $no => $val) {
                            $answers[$no] = $val;
                        }
                    }

                    // 3. Process answers through DecisionTreeService to ensure exact computed results match
                    $processed = $dtService->process($answers);

                    // 4. Create Screening
                    $screening = Screening::create([
                        'patient_id'       => $patient->id,
                        'started_at'       => $currentDate->copy()->subMinutes(rand(3, 8)),
                        'last_activity_at' => $currentDate,
                        'selesai_at'       => $currentDate,
                        'created_at'       => $currentDate,
                        'updated_at'       => $currentDate,
                    ]);

                    // 5. Bulk insert Answers for speed
                    $answersData = [];
                    foreach ($answers as $no => $nilai) {
                        $answersData[] = [
                            'screening_id' => $screening->id,
                            'question_id'  => $questionIdMap[$no],
                            'nilai'         => $nilai,
                            'created_at'   => $currentDate,
                            'updated_at'   => $currentDate,
                        ];
                    }
                    DB::table('answers')->insert($answersData);

                    // 6. Create computed Result record
                    Result::create([
                        'screening_id'   => $screening->id,
                        'skor_depresi'   => $processed['skor_d'],
                        'skor_kecemasan' => $processed['skor_a'],
                        'skor_stres'     => $processed['skor_s'],
                        'kat_depresi'    => $processed['kat_d'],
                        'kat_kecemasan'  => $processed['kat_a'],
                        'kat_stres'      => $processed['kat_s'],
                        'rekomendasi'    => $processed['rekomendasi'],
                        'created_at'     => $currentDate,
                        'updated_at'     => $currentDate,
                    ]);

                    // Increment week counter and advance date by exactly 7 days + slight hour variation to look organic
                    $currentWeek++;
                    $currentDate = $currentDate->copy()->addDays(7)->addHours(rand(-6, 6));
                }
            }

            DB::commit();
            $this->command->info('✓ Seeding 50 pasien dan log screening mingguan berhasil diselesaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error saat seeding: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Distribute target score randomly across 7 questions (max 3 points each)
     */
    private function distributeRawScore(array $questionNumbers, int $targetRawScore): array
    {
        $answers = [];
        foreach ($questionNumbers as $no) {
            $answers[$no] = 0;
        }

        $remaining = min($targetRawScore, 21);
        while ($remaining > 0) {
            $no = $questionNumbers[array_rand($questionNumbers)];
            if ($answers[$no] < 3) {
                $answers[$no]++;
                $remaining--;
            }
        }

        return $answers;
    }
}
