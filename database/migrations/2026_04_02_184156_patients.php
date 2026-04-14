<?php // 2026_04_01_083153_create_patients_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique()->index();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
