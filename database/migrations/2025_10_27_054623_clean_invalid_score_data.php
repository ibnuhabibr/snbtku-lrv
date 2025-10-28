<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean invalid score data in user_tryouts table
        // Set score to NULL for records that contain non-numeric characters
        DB::statement("UPDATE user_tryouts SET score = NULL WHERE score LIKE '%:%' OR score = ''");
        
        // Clean invalid score data in user_subtest_progress table
        DB::statement("UPDATE user_subtest_progress SET score = NULL WHERE score LIKE '%:%' OR score = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
