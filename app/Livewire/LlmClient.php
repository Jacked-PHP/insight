<?php

namespace App\Livewire;

use App\Enums\MessageType;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Livewire\Component;
use Ramsey\Uuid\Uuid;

class LlmClient extends Component
{
    public int $userId;
    public string $host;
    public string $protocol;
    public int $port;
    public string $channel;
    public array $participants;
    public string $chatUuid;
    public array $messages;
    public string $response;
    public string $chatName;

    public function mount(string $chat)
    {
        $this->chatUuid = $chat;
        $chat = auth()->user()->chats()->where('uuid', $this->chatUuid)->first();

        /** @var User $user */
        $user = auth()->user();

        $this->participants = [
            [
                'type' => 'bot',
                'uuid' => 'bot-alfred',
                'name' => 'Alfred',
                'avatar' => '',
            ],
            [
                'type' => 'user',
                'uuid' => $user->id,
                'name' => $user->name,
                'avatar' => '',
            ]
        ];

        $this->loadMessages($chat);

        $this->userId = $user->id;
        $this->host = config('jacked-server.host');
        $this->protocol = config('jacked-server.ssl-enabled') ? 'wss' : 'ws';
        $this->port = config('jacked-server.ssl-enabled') ? config('jacked-server.ssl-port') : config('jacked-server.port');
        $this->channel = 'private-chat-channel.' . $user->id;
        $this->chatName = $chat->name;
    }

    public function loadMessages(?Chat $chat = null)
    {
        $chat = $chat ?? auth()->user()->chats()->where('uuid', $this->chatUuid)->first();
        $this->messages = $chat->messages->map([$this, 'formatMessage'])->toArray();
    }

    public function formatMessage(Message $message): array
    {
        return [
            'uuid' => $message->uuid,
            'message' => $message->content,
            'timestamp' => $message->created_at->toDateTimeString(),
            'type' => $message->type->value,
            'sender' => [
                'type' => $message->user_id === null ? 'bot' : 'user',
                'uuid' => $message->user_id ?? 'bot-alfred',
                'name' => $message->user === null ? 'Alfred' : $message->user->name,
                'avatar' => '',
            ],
        ];
    }

    public function postMessage(string $message)
    {
        if (empty($message)) {
            return;
        }

        $chat = Chat::where('uuid', $this->chatUuid)
            ->where('user_id', $this->userId)
            ->first();

        $messageRecord = $chat->messages()->create([
            'uuid' => Uuid::uuid4()->toString(),
            'user_id' => $this->userId,
            'content' => $message,
            'type' => MessageType::REQUEST,
        ]);

        $chat->messages()->create([
            'uuid' => Uuid::uuid4()->toString(),
            'user_id' => null,
            'content' => '',
            'type' => MessageType::RESPONSE,
            'parent_id' => $messageRecord->id,
        ]);

        $this->loadMessages($chat);
    }

    public function render()
    {
        return view('livewire.llm-client', [
            'messages' => $this->messages,
        ]);
    }
}
