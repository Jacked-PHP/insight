<?php

namespace App\Jobs;

use App\Events\ChatMessageEventStream;
use App\Models\Chat;
use App\Models\Message;
use Cloudstudio\Ollama\Facades\Ollama;
use Hook\Filter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

class SendChatMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $chatUuid,
        protected int $userId,
        protected Message $messageRecord,
        protected Message $responseRecord,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(?callable $callback = null): void
    {
        $finalMessage = '';

        $this->chat($this->messageRecord->content, function ($response) use ($callback, &$finalMessage) {
            if ('bot-finished' !== $response) {
                $finalMessage .= $response;

                if ($callback !== null) {
                    $callback($finalMessage);
                } else {
                    event(new ChatMessageEventStream(
                        userId: null,
                        messageUuuid: $this->messageRecord->uuid,
                        message: $finalMessage,
                        channel: 'chat-channel.' . $this->userId
                    ));
                }
                return;
            }

            $this->responseRecord->update([
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

        if ('ollama' === config('llm.ai_api')) {
            $this->chatOllama($messages, $callback);
        } elseif ('openai' === config('llm.ai_api')) {
            $this->chatOpenai($messages, $callback);
        }
    }

    public function chatOpenai(array $messages, callable $callback): void
    {
        $stream = OpenAI::chat()->createStreamed([
            'model' => config('llm.openai-model'),
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

    public function chatOllama(array $messages, callable $callback): void
    {
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
                if (config('app.debug')) {
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
    }
}
