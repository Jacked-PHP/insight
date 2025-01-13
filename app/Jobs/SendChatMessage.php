<?php

namespace App\Jobs;

use App\Enums\MessageType;
use App\Events\ChatMessageEventStream;
use App\Models\Chat;
use App\Models\Message;
use App\Services\ResourceLibrary;
use LLPhant\Chat\Message as LlphantMessage;
use Exception;
use Hook\Filter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LLPhant\Chat\OllamaChat;
use LLPhant\Chat\OpenAIChat;
use LLPhant\OllamaConfig;
use LLPhant\OpenAIConfig;
use Psr\Http\Message\StreamInterface;

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

        $chat = Chat::where('uuid', $this->chatUuid)->first();

        $systemMessage = Filter::applyFilters('system-message', "You are an assistant with access to specific knowledge. Always prioritize the context when responding.", $prompt);

        $messages = $chat
            ->messages
            ->prepend(Message::factory()->make([
                'type' => 'system',
                'content' => str_replace('{CONTEXT_PLACEHOLDER}', '', $systemMessage),
                'chat_id' => $chat->id,
                'user_id' => auth()->id(),
            ]))
            ->map(function ($message) {
                if ($message->type === MessageType::SYSTEM) {
                    return LlphantMessage::system($message->content);
                } elseif ($message->type === MessageType::RESPONSE) {
                    return LlphantMessage::assistant($message->content);
                }

                return LlphantMessage::user($message->content);
            })
            ->push(LlphantMessage::user($prompt))
            ->toArray();

        $messages = Filter::applyFilters('chat-messages', $messages, $prompt);
        logger()->debug('messages', ['messages' => $messages]);

        if ('ollama' === config('llm.ai_api')) {
            $this->chatOllama($messages, $callback);
        } elseif ('openai' === config('llm.ai_api')) {
            $this->chatOpenai($messages, $callback);
        }
    }

    public function chatOpenai(array $messages, callable $callback): void
    {
        try {
            $config = new OpenAIConfig();
            $config->model = config('llm.openai-model');
            $config->apiKey = config('openai.api_key');
            $chat = new OpenAIChat($config);
            /** @var StreamInterface $response */
            $response = $chat->generateChatStream($messages);
        } catch (Exception $e) {
            $callback($e->getMessage());
            $callback('bot-finished');
            return;
        }

        while (!$response->eof()) {
            $callback($response->read(1));
        }

        $callback('bot-finished');
    }

    public function chatOllama(array $messages, callable $callback): void
    {
        try {
            $config = new OllamaConfig();
            $config->model = config('ollama-laravel.model');
            $config->url = config('ollama-laravel.url') . '/api/';
            $chat = new OllamaChat($config);
            /** @var StreamInterface $response */
            $response = $chat->generateChatStream($messages);
        } catch (Exception $e) {
            $callback($e->getMessage());
            $callback('bot-finished');
            return;
        }

        while (!$response->eof()) {
            $callback($response->read(1));
        }

        $callback('bot-finished');
    }
}
