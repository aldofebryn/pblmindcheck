<?php // 2026_04_01_083451_create_results_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('screening_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('skor_depresi');
            $table->unsignedTinyInteger('skor_kecemasan');
            $table->unsignedTinyInteger('skor_stres');
            $table->enum('kat_depresi',   ['Normal', 'Ringan', 'Sedang', 'Berat', 'Sangat Berat']);
            $table->enum('kat_kecemasan', ['Normal', 'Ringan', 'Sedang', 'Berat', 'Sangat Berat']);
            $table->enum('kat_stres',     ['Normal', 'Ringan', 'Sedang', 'Berat', 'Sangat Berat']);
            $table->enum('rekomendasi',   ['R16', 'R17', 'R18']);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
