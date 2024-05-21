<?php

namespace App\Livewire;

use App\Enums\MessageType;
use App\Events\ChatMessageEvent;
use App\Jobs\SendChatMessage;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OllamaClient extends Component
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

    public function mount(string $chat)
    {
        $this->chatUuid = $chat;

        /** @var User $user */
        $user = auth()->user();
        // TODO: just a test thing...
        if ($user === null) {
            Auth::loginUsingId(1);
            $user = auth()->user();
        }

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

        $this->loadMessages();

        $this->userId = $user->id;
        $this->host = config('jacked-server.host');
        $this->protocol = config('jacked-server.ssl-enabled') ? 'wss' : 'ws';
        $this->port = config('jacked-server.ssl-enabled') ? config('jacked-server.ssl-port') : config('jacked-server.port');
        $this->channel = 'private-chat-channel.' . $user->id;
    }

    public function loadMessages()
    {
        $this->messages = auth()->user()->chats()->where('uuid', $this->chatUuid)->first()
            ->messages
            ->map([$this, 'formatMessage'])
            ->toArray();
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

        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.ollama-client', [
            'messages' => $this->messages,
        ]);
    }
}
