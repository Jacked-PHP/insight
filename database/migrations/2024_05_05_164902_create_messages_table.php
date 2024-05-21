<?php

use App\Enums\MessageType;
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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 60)->unique();
            $table->foreignId('chat_id')->constrained('chats');
            $table->longText('content');
            $table->foreignId('parent_id')
                ->nullable()
                ->comment('This is used to store the parent message id if this message is a response to another message')
                ->constrained('messages');
            $table->enum('type', [MessageType::REQUEST->value, MessageType::RESPONSE->value]);
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
