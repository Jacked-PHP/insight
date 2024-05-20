<?php

namespace App\Jobs;

use App\Events\ChatMessageEvent;
use App\Models\Chat;
use Cloudstudio\Ollama\Facades\Ollama;
use Hook\Filter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;
use Ramsey\Uuid\Uuid;

class SendChatMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $chatUuid,
        protected int $userId,
        protected string $message,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $chat = Chat::where('uuid', $this->chatUuid)
            ->where('user_id', $this->userId)
            ->first();
        $messageRecord = $chat->messages()->create([
            'uuid' => Uuid::uuid4()->toString(),
            'user_id' => $this->userId,
            'content' => $this->message,
        ]);

        $finalMessage = '';
        $this->chat($messageRecord->content, function ($response) use ($chat, $messageRecord, &$finalMessage) {
            if ('bot-finished' !== $response) {
                $response = htmlspecialchars($response);
                $finalMessage .= $response;
                event(new ChatMessageEvent(
                    userId: null,
                    messageUuuid: $messageRecord->uuid,
                    message: $response,
                    channel: 'chat-channel.' . $this->userId
                ));
                return;
            }

            $chat->messages()->create([
                'uuid' => Uuid::uuid4()->toString(),
                'user_id' => null,
                'content' => $finalMessage,
            ]);
        });
    }

    private function chat(string $prompt, callable $callback): void
    {
        $prompt = Filter::applyFilters('chat-prompt', $prompt);

        $messages = Chat::where('uuid', $this->chatUuid)
            ->first()
            ->messages
            ->map(function ($message) {
                return [
                    'role' => $message->user_id === null ? 'assistant' : 'user',
                    'content' => $message->content,
                ];
            })
            ->push([
                'role' => 'user',
                'content' => $prompt,
            ])
            ->toArray();

        $messages = Filter::applyFilters('chat-messages', $messages, $prompt);

        if ('ollama' === config('ollama-laravel.ai_api')) {

            $response = Ollama::agent(config('ollama-laravel.agent'))
                ->model(config('ollama-laravel.model'))
                ->stream(true)
                ->chat($messages);

            $body = $response->getBody();
            $buffer = '';
            while (!$body->eof()) {
                $buffer .= $body->read(1);
                if (substr($buffer, -1) !== PHP_EOL) {
                    continue;
                }

                $buffer = trim($buffer);
                if (str_starts_with($buffer, 'data:')) {
                    $buffer = trim(str_replace('data:', '',$buffer));
                }
                $jsonObject = json_decode($buffer, true);
                if (!$jsonObject) {
                    if (config('ollama-laravel.debug')) {
                        echo "Error decoding JSON: " . json_last_error_msg() . PHP_EOL;
                        echo "Message Received: " . PHP_EOL;
                        var_dump($buffer);
                    }
                    $buffer = '';
                    continue;
                }
                $buffer = '';

                $callback(Arr::get($jsonObject, 'message.content', ''));
            }

            $callback('bot-finished');

        } elseif ('openai' === config('ollama-laravel.ai_api')) {

            $stream = OpenAI::chat()->createStreamed([
                'model' => config('ollama-laravel.model'),
                'messages' => $messages,
            ]);

            foreach ($stream as $response) {
                if ($response->choices[0]->finishReason === 'stop') {
                    $callback('bot-finished');
                    break;
                }

                $callback($response->choices[0]->delta->content);
            }

        }
    }
}
