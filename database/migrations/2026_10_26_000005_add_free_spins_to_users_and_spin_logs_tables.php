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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('free_spins')->default(5)->after('balance');
        });

        Schema::table('spin_logs', function (Blueprint $table) {
            $table->boolean('is_free_spin')->default(false)->after('multiplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('free_spins');
        });

        Schema::table('spin_logs', function (Blueprint $table) {
            $table->dropColumn('is_free_spin');
        });
    }
};
