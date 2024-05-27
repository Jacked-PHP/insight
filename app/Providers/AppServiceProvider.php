<?php

namespace App\Providers;

use App\Services\ResourceLibrary;
use Hook\Filter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind('main-menu', function () {
            $menu = [
                'primary' => [
                    [
                        'name' => 'New Chat',
                        'route' => 'home',
                        'route-params' => [],
                        'current' => Request::route()->named('home'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 shrink-0 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>',
                    ],
                ], // primary
            ];

            $chats = auth()->user()->chats()->latest()->limit(10)->get();
            $menu['chats'] = [];
            foreach ($chats as $chat) {
                $current = Context::get('chat-uuid') === $chat->uuid;
                $menu['chats'][] = [
                    'name' => $chat->name,
                    'route' => 'chat',
                    'route-params' => ['chat' => $chat->uuid],
                    'current' => $current,
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 shrink-0 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>
                    ',
                ];
            }

            return $menu;
        });

        // Filter::addFilter('chat-messages', function (array $messages, string $prompt) {
        //     $messages[] = [
        //         'role' => 'system',
        //         'content' => 'If you don\'t know, say you don\'t know. Always stay in the context given to you, and prioritize content that I share with you when responding. Be objective.',
        //     ];
        //     return $messages;
        // });

        // Filter::addFilter('chat-messages', function (array $messages, string $prompt) {
        //     $results = (new ResourceLibrary)->search($prompt);
        //     if (count($results) === 0) {
        //         return $messages;
        //     }
        //
        //     $enhancement = 'CONTEXT: ' . PHP_EOL;
        //     foreach ($results as $result) {
        //         $enhancement .= $result->text . PHP_EOL;
        //     }
        //
        //     $messages[] = [
        //         'role' => 'system',
        //         'content' => $enhancement,
        //     ];
        //
        //     logger('messages', ['messages' => $messages]);
        //     return $messages;
        // });

        // Filter::addFilter('chat-prompt', function (string $prompt) {
        //     $results = (new ResourceLibrary)->search($prompt);
        //     if (count($results) === 0) {
        //         return $prompt;
        //     }
        //
        //     $enhancement = 'CONTEXT: ' . PHP_EOL;
        //     foreach ($results as $result) {
        //         $enhancement .= $result->text . PHP_EOL;
        //     }
        //     $prompt = $prompt . PHP_EOL . $enhancement;
        //
        //     logger()->info('test', ['prompt' => $prompt]);
        //
        //     return $prompt;
        // });
    }
}
