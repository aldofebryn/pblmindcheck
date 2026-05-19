<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
            // No index needed, foreignId creates it automatically if constrained
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('screenings');
    }
};
