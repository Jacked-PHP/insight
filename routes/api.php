<?php

use App\Jobs\SendChatMessage;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

Route::get('/stream-endpoint/{chatUuid}/{messageUuid}/{responseUuid}', function (string $chatUuid, string $messageUuid, string $responseUuid) {
    $user = auth()->user();

    $chat = $user->chats()->where('uuid', $chatUuid)->first();

    $messageRecord = $chat->messages()->where('uuid', $messageUuid)->first();

    $responseRecord = $chat->messages()->where('uuid', $responseUuid)->first();

    $this->response = '';
    $response = new StreamedResponse(function () use ($user, $chatUuid, $messageRecord, $responseRecord) {
        (new SendChatMessage(
            chatUuid: $chatUuid,
            userId: $user->id,
            messageRecord: $messageRecord,
            responseRecord: $responseRecord,
        ))->handle(callback: function (string $chunk) {
            echo $chunk;
            ob_flush();
            flush();
        });
    });

    $response->headers->set('Transfer-Encoding', 'chunked');
    $response->send();
})->middleware('web');
