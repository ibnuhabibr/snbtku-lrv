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
        Schema::table('user_tryouts', function (Blueprint $table) {
            // Ubah kolom score dari decimal menjadi string untuk menyimpan format "jumlah benar : total soal"
            $table->string('score', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tryouts', function (Blueprint $table) {
            // Kembalikan ke tipe decimal jika rollback
            $table->decimal('score', 5, 2)->nullable()->change();
        });
    }
};
