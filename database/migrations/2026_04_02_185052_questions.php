<?php // 2026_04_01_083308_create_questions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nomor')->unique();
            $table->text('teks_id');
            $table->text('teks_en');
            $table->enum('subskala', ['Depression', 'Anxiety', 'Stress']);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
