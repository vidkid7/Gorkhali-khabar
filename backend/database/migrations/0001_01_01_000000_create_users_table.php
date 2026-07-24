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
        // The legacy-compatible migration owns these tables and never drops them.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Legacy tables are intentionally never removed by rollback.
    }
};
