<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'chat_id' => Chat::factory(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'type' => $this->faker->randomElement(['text', 'image', 'video']),
            'content' => $this->faker->text,
        ];
    }
}
