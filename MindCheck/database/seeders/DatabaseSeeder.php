<?php

namespace Database\Seeders;

use App\Models\{Admin, Question};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin default
        Admin::firstOrCreate(['email' => 'admin@mindcheck.id'], [
            'name'     => 'Administrator',
            'password' => Hash::make('mindcheck2026'),
        ]);

        // 21 pertanyaan DASS-21 (Lovibond & Lovibond, 1995)
        $qs = [
            [
                1,
                'S',
                'Saya merasa sulit untuk rileks / bersantai',
                'I found it hard to wind down'
            ],
            [
                2,
                'A',
                'Saya merasakan mulut saya kering',
                'I was aware of dryness of my mouth'
            ],
            [
                3,
                'D',
                'Saya tidak dapat merasakan perasaan positif sama sekali',
                "I couldn't seem to experience any positive feeling at all"
            ],
            [
                4,
                'A',
                'Saya mengalami kesulitan bernapas tanpa aktivitas fisik',
                'I experienced breathing difficulty in absence of exertion'
            ],
            [
                5,
                'D',
                'Saya merasa sulit untuk berinisiatif dalam melakukan sesuatu',
                'I found it difficult to work up the initiative to do things'
            ],
            [
                6,
                'S',
                'Saya cenderung bereaksi berlebihan terhadap situasi',
                'I tended to over-react to situations'
            ],
            [
                7,
                'A',
                'Saya mengalami gemetar (misalnya pada tangan)',
                'I experienced trembling (e.g. in the hands)'
            ],
            [
                8,
                'S',
                'Saya merasa menggunakan banyak energi saraf / mental',
                'I felt I was using a lot of nervous energy'
            ],
            [
                9,
                'A',
                'Saya khawatir tentang situasi di mana saya mungkin panik dan mempermalukan diri sendiri',
                'I was worried about situations in which I might panic and make a fool of myself'
            ],
            [
                10,
                'D',
                'Saya merasa tidak ada hal yang dapat saya nantikan',
                'I felt that I had nothing to look forward to'
            ],
            [
                11,
                'S',
                'Saya mendapati diri saya menjadi gelisah / mudah terusik',
                'I found myself getting agitated'
            ],
            [
                12,
                'S',
                'Saya merasa sulit untuk rileks',
                'I found it difficult to relax'
            ],
            [
                13,
                'D',
                'Saya merasa sedih dan tertekan',
                'I felt down-hearted and blue'
            ],
            [
                14,
                'S',
                'Saya tidak toleran terhadap apapun yang menghalangi aktivitas saya',
                'I was intolerant of anything that kept me from getting on with what I was doing'
            ],
            [
                15,
                'A',
                'Saya merasa hampir panik',
                'I felt I was close to panic'
            ],
            [
                16,
                'D',
                'Saya tidak dapat menjadi antusias tentang apapun',
                'I was unable to become enthusiastic about anything'
            ],
            [
                17,
                'D',
                'Saya merasa diri saya tidak berharga sebagai seseorang',
                "I felt I wasn't worth much as a person"
            ],
            [
                18,
                'S',
                'Saya merasa agak sensitif / mudah tersinggung',
                'I felt that I was rather touchy'
            ],
            [
                19,
                'A',
                'Saya menyadari adanya detak jantung tanpa aktivitas fisik',
                'I was aware of the action of my heart in the absence of physical exertion'
            ],
            [
                20,
                'A',
                'Saya merasa takut tanpa alasan yang jelas',
                'I felt scared without any good reason'
            ],
            [
                21,
                'D',
                'Saya merasa hidup tidak berharga',
                'I felt that life was meaningless'
            ],
        ];

        $map = ['D' => 'Depression', 'A' => 'Anxiety', 'S' => 'Stress'];

        foreach ($qs as [$nomor, $sub, $id, $en]) {
            Question::updateOrCreate(['nomor' => $nomor], [
                'teks_id'  => $id,
                'teks_en'  => $en,
                'subskala' => $map[$sub],
            ]);
        }

        $this->command->info('✓ Admin default + 21 pertanyaan DASS-21 berhasil di-seed.');
    }
}
