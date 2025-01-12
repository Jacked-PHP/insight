<?php

use App\Models\Asset;
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
        Schema::table('assets', function (Blueprint $table) {
            $table->boolean('synced_at')->nullable();
            $table->integer('embedding_id')
                ->nullable()
                ->comment('The embedding ID of the asset. This is an external reference.');
        });

        Asset::query()->update([
            'synced_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('synced_at');
            $table->dropColumn('embedding_id');
        });
    }
};
