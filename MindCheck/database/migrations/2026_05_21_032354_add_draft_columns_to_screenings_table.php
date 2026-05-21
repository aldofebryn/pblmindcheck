<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screenings', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('patient_id');
            $table->timestamp('last_activity_at')->nullable()->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('screenings', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'last_activity_at']);
        });
    }
};