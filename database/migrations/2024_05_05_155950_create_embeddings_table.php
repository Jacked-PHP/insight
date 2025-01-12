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
        // IMPORTANT: Only for postres!
        DB::connection('pgsql')->statement('CREATE EXTENSION IF NOT EXISTS vector;');
        DB::connection('pgsql')->statement('CREATE TABLE IF NOT EXISTS embeddings (
            id bigserial PRIMARY KEY,
            embedding vector(1024),
            text TEXT,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
        );');

        // IMPORTANT: Only for SQLite-vec
        // DB::connection('vector')->statement('CREATE TABLE IF NOT EXISTS embeddings (
        //     id INTEGER PRIMARY KEY AUTOINCREMENT,
        //     embedding float[8],
        //     text TEXT,
        //     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        //     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        // );');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql')->statement('DROP TABLE IF EXISTS embeddings;');

        // DB::connection('vector')->statement('DROP TABLE IF EXISTS embeddings;');
    }
};
