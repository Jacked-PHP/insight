<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // IMPORTANT: Only for postres
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');

        DB::statement('CREATE TABLE IF NOT EXISTS embeddings (
            id bigserial PRIMARY KEY,
            embedding vector(1024),
            text TEXT,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
        );');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS embeddings;');
    }
};
