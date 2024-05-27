<?php

namespace App\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;

class ChatContext
{
    public function handle(Request $request, Closure $next)
    {
        $chat = Route::getRoutes()->match($request)->parameter('chat');
        if (null !== $chat) {
            Context::add('chat-uuid', $chat);
        }

        return $next($request);
    }
}
