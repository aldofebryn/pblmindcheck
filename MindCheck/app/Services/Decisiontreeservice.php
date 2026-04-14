<?php

namespace App\Services;

/**
 * ============================================================
 * Decision Tree Service — DASS-21  |  MindCheck
 * ============================================================
 * Mengimplementasikan pohon keputusan (decision tree) untuk
 * mengklasifikasikan hasil kuesioner DASS-21 menjadi kategori
 * keparahan per subskala dan rekomendasi tindak lanjut.
 *
 * Struktur pohon biner:
 *   TreeNode (split) → nilai <= threshold → cabang kiri (left)
 *                    → nilai >  threshold → cabang kanan (right)
 *   TreeNode (leaf)  → label hasil akhir
 * ============================================================
 */

// ─── Representasi node pohon keputusan ────────────────────────
final class TreeNode
{
    public function __construct(
        public readonly string    $type,       // 'split' | 'leaf'
        public readonly ?float    $threshold,  // hanya untuk split node
        public readonly ?TreeNode $left,       // skor <= threshold
        public readonly ?TreeNode $right,      // skor >  threshold
        public readonly ?string   $label,      // hanya untuk leaf node
        public readonly array     $meta = [],  // info tambahan (narasi, warna)
    ) {}

    public static function split(float $threshold, TreeNode $left, TreeNode $right): self
    {
        return new self('split', $threshold, $left, $right, null);
    }

    public static function leaf(string $label, array $meta = []): self
    {
        return new self('leaf', null, null, null, $label, $meta);
    }

    /** Traversal: telusuri pohon berdasarkan nilai numerik */
    public function traverse(float $value): self
    {
        if ($this->type === 'leaf') return $this;
        return $value <= $this->threshold
            ? $this->left->traverse($value)
            : $this->right->traverse($value);
    }
}

// ─── Service utama ─────────────────────────────────────────────
class DecisionTreeService
{
    // Distribusi nomor soal per subskala (sesuai Lovibond & Lovibond, 1995)
    private const MAP = [
        'depresi'   => [3, 5, 10, 13, 16, 17, 21],
        'kecemasan' => [2,  4,  7,  9, 15, 19, 20],
        'stres'     => [1,  6,  8, 11, 12, 14, 18],
    ];

    // ── Pohon Depresi ──────────────────────────────────────────
    private static function treeDep(): TreeNode
    {
        return TreeNode::split(
            9,
            TreeNode::leaf('Normal',       ['warna' => 'green',  'skor_max' => 9]),
            TreeNode::split(
                13,
                TreeNode::leaf('Ringan',   ['warna' => 'blue',   'skor_max' => 13]),
                TreeNode::split(
                    20,
                    TreeNode::leaf('Sedang',   ['warna' => 'yellow', 'skor_max' => 20]),
                    TreeNode::split(
                        27,
                        TreeNode::leaf('Berat',        ['warna' => 'orange', 'skor_max' => 27]),
                        TreeNode::leaf('Sangat Berat', ['warna' => 'red',    'skor_max' => 42]),
                    ),
                ),
            ),
        );
    }

    // ── Pohon Kecemasan ────────────────────────────────────────
    private static function treeAnx(): TreeNode
    {
        return TreeNode::split(
            7,
            TreeNode::leaf('Normal',       ['warna' => 'green',  'skor_max' => 7]),
            TreeNode::split(
                9,
                TreeNode::leaf('Ringan',   ['warna' => 'blue',   'skor_max' => 9]),
                TreeNode::split(
                    14,
                    TreeNode::leaf('Sedang',   ['warna' => 'yellow', 'skor_max' => 14]),
                    TreeNode::split(
                        19,
                        TreeNode::leaf('Berat',        ['warna' => 'orange', 'skor_max' => 19]),
                        TreeNode::leaf('Sangat Berat', ['warna' => 'red',    'skor_max' => 42]),
                    ),
                ),
            ),
        );
    }

    // ── Pohon Stres ────────────────────────────────────────────
    private static function treeStr(): TreeNode
    {
        return TreeNode::split(
            14,
            TreeNode::leaf('Normal',       ['warna' => 'green',  'skor_max' => 14]),
            TreeNode::split(
                18,
                TreeNode::leaf('Ringan',   ['warna' => 'blue',   'skor_max' => 18]),
                TreeNode::split(
                    25,
                    TreeNode::leaf('Sedang',   ['warna' => 'yellow', 'skor_max' => 25]),
                    TreeNode::split(
                        33,
                        TreeNode::leaf('Berat',        ['warna' => 'orange', 'skor_max' => 33]),
                        TreeNode::leaf('Sangat Berat', ['warna' => 'red',    'skor_max' => 42]),
                    ),
                ),
            ),
        );
    }

    // ── Pohon Rekomendasi (berdasarkan 3 kategori) ────────────
    private static function treeRec(string $d, string $a, string $s): string
    {
        $berat = ['Berat', 'Sangat Berat'];

        // Cabang 1: ada minimal satu subskala Berat / Sangat Berat
        if (in_array($d, $berat) || in_array($a, $berat) || in_array($s, $berat)) {
            return 'R16';
        }
        // Cabang 2: ada minimal satu Sedang (tidak ada Berat)
        if ($d === 'Sedang' || $a === 'Sedang' || $s === 'Sedang') {
            return 'R17';
        }
        // Cabang 3: semua Normal atau Ringan
        return 'R18';
    }

    // ──────────────────────────────────────────────────────────
    // Metode publik utama
    // ──────────────────────────────────────────────────────────

    /**
     * Proses jawaban DASS-21 melalui decision tree.
     *
     * @param array $answers  [ nomor_soal (int) => nilai 0–3 ]
     * @return array {
     *   skor_d, skor_a, skor_s           : int (final, sudah ×2)
     *   kat_d,  kat_a,  kat_s            : string kategori
     *   rekomendasi                       : string 'R16'|'R17'|'R18'
     *   teks_rekomendasi                  : string narasi
     *   trace                             : array log langkah pohon
     * }
     */
    public function process(array $answers): array
    {
        $trace = [];

        // ── Step 1: Hitung skor mentah per subskala ──────────
        $rawD = $this->rawScore($answers, self::MAP['depresi']);
        $rawA = $this->rawScore($answers, self::MAP['kecemasan']);
        $rawS = $this->rawScore($answers, self::MAP['stres']);
        $trace[] = "Skor mentah  → D:{$rawD}  A:{$rawA}  S:{$rawS}";

        // ── Step 2: Konversi ×2 (DASS-21 → skala DASS-42) ───
        $skor_d = $rawD * 2;
        $skor_a = $rawA * 2;
        $skor_s = $rawS * 2;
        $trace[] = "Skor final×2 → D:{$skor_d}  A:{$skor_a}  S:{$skor_s}";

        // ── Step 3: Traversal pohon per subskala ─────────────
        $leafD = self::treeDep()->traverse($skor_d);
        $leafA = self::treeAnx()->traverse($skor_a);
        $leafS = self::treeStr()->traverse($skor_s);
        $kat_d = $leafD->label;
        $kat_a = $leafA->label;
        $kat_s = $leafS->label;
        $trace[] = "Pohon Depresi   → threshold path → Kategori: {$kat_d}";
        $trace[] = "Pohon Kecemasan → threshold path → Kategori: {$kat_a}";
        $trace[] = "Pohon Stres     → threshold path → Kategori: {$kat_s}";

        // ── Step 4: Traversal pohon rekomendasi ───────────────
        $rekomendasi = self::treeRec($kat_d, $kat_a, $kat_s);
        $trace[] = "Pohon Rekomendasi → {$rekomendasi}";

        return [
            'skor_d'           => $skor_d,
            'skor_a'           => $skor_a,
            'skor_s'           => $skor_s,
            'kat_d'            => $kat_d,
            'kat_a'            => $kat_a,
            'kat_s'            => $kat_s,
            'warna_d'          => $leafD->meta['warna'] ?? 'gray',
            'warna_a'          => $leafA->meta['warna'] ?? 'gray',
            'warna_s'          => $leafS->meta['warna'] ?? 'gray',
            'rekomendasi'      => $rekomendasi,
            'teks_rekomendasi' => self::tekstRek($rekomendasi),
            'trace'            => $trace,
        ];
    }

    // ── Utilitas ───────────────────────────────────────────────
    private function rawScore(array $answers, array $nomorSoal): int
    {
        return array_sum(array_map(
            fn($n) => (int)($answers[$n] ?? 0),
            $nomorSoal
        ));
    }

    public static function tekstRek(string $kode): string
    {
        return match ($kode) {
            'R16' => 'Kondisi Anda memerlukan perhatian segera. Segera konsultasikan dengan psikiater atau psikolog klinis. Jika membutuhkan bantuan darurat, hubungi SEJIWA 119 ext 8 (tersedia 24 jam).',
            'R17' => 'Disarankan untuk berkonsultasi dengan psikolog atau konselor. Kondisi ini dapat ditangani dengan baik jika ditindaklanjuti sedini mungkin.',
            'R18' => 'Kondisi Anda berada dalam rentang yang dapat dikelola secara mandiri. Pertahankan gaya hidup sehat dan lakukan skrining ulang dalam 2–4 minggu ke depan.',
            default => '',
        };
    }

    /** Kembalikan kelas Tailwind untuk badge kategori */
    public static function badgeClass(string $kat): string
    {
        return match ($kat) {
            'Normal'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Ringan'       => 'bg-sky-50 text-sky-700 border-sky-200',
            'Sedang'       => 'bg-amber-50 text-amber-700 border-amber-200',
            'Berat'        => 'bg-orange-50 text-orange-700 border-orange-200',
            'Sangat Berat' => 'bg-red-50 text-red-700 border-red-200',
            default        => 'bg-gray-50 text-gray-600 border-gray-200',
        };
    }

    /** Kembalikan hex warna untuk chart */
    public static function chartColor(string $kat): string
    {
        return match ($kat) {
            'Normal'       => '#10b981',
            'Ringan'       => '#0ea5e9',
            'Sedang'       => '#f59e0b',
            'Berat'        => '#f97316',
            'Sangat Berat' => '#ef4444',
            default        => '#9ca3af',
        };
    }
}
