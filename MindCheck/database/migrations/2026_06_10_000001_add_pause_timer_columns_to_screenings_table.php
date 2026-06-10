<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screenings', function (Blueprint $table) {
            $table->integer('remaining_seconds')->nullable()->after('last_activity_at');
            $table->timestamp('timer_started_at')->nullable()->after('remaining_seconds');
            $table->unsignedTinyInteger('last_answered_question')->nullable()->after('timer_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('screenings', function (Blueprint $table) {
            $table->dropColumn([
                'remaining_seconds',
                'timer_started_at',
                'last_answered_question',
            ]);
        });
    }
};
