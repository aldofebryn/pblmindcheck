<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('admin_notes');
            $table->string('password')->nullable()->after('username');
            $table->integer('umur')->nullable()->after('password');
            $table->string('status_pekerjaan')->nullable()->after('umur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['username', 'password', 'umur', 'status_pekerjaan']);
        });
    }
};
