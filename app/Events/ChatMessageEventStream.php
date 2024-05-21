<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;

class ChatMessageEventStream
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public ?int $userId,
        public string $messageUuuid,
        public string $message,
        public string $channel,
    ) {
    }

    public function broadcastWith(): array
    {
        return [
            'userId' => $this->userId,
            'messageUuid' => $this->messageUuuid,
            'message' => $this->message,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
