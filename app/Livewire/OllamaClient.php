<?php

namespace App\Livewire;

use App\Jobs\SendChatMessage;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Livewire\Component;

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

    public function mount(string $chat)
    {
        $this->chatUuid = $chat;

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

        $this->messages = Chat::where('uuid', $chat)->first()
            ->messages
            ->map(function (Message $message) {
                return [
                    'uuid' => $message->uuid,
                    'message' => $message->content,
                    'timestamp' => $message->created_at->toDateTimeString(),
                    'sender' => [
                        'type' => $message->user_id === null ? 'bot' : 'user',
                        'uuid' => $message->user_id ?? 'bot-alfred',
                        'name' => $message->user === null ? 'Alfred' : $message->user->name,
                        'avatar' => '',
                    ],
                ];
            })->toArray();

        $this->userId = $user->id;
        $this->host = config('jacked-server.host');
        $this->protocol = config('jacked-server.ssl-enabled') ? 'wss' : 'ws';
        $this->port = config('jacked-server.ssl-enabled') ? config('jacked-server.ssl-port') : config('jacked-server.port');
        $this->channel = 'private-chat-channel.' . $user->id;
    }

    public function postMessage(string $message): void
    {
        dispatch(new SendChatMessage(
            chatUuid: $this->chatUuid,
            userId: $this->userId,
            message: $message,
        ));
    }

    public function render()
    {
        return view('livewire.ollama-client');
    }
}
