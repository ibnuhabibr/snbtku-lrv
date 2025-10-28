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
        Schema::table('user_subtest_progress', function (Blueprint $table) {
            // Ubah kolom score dari decimal(5,2) ke string(50)
            $table->string('score', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subtest_progress', function (Blueprint $table) {
            // Kembalikan ke decimal(5,2) jika rollback
            $table->decimal('score', 5, 2)->nullable()->change();
        });
    }
};
