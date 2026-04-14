<?php // 2026_04_01_083413_create_screenings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('screenings', function (Blueprint $table) {
            $table->id();
            $table->uuid('patient_token');
            $table->foreign('patient_token')->references('token')->on('patients')->cascadeOnDelete();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
            $table->index('patient_token');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('screenings');
    }
};
