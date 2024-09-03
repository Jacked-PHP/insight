<?php

use App\Livewire\Library;
use App\Livewire\LoginForm;
use App\Livewire\LlmClient;
use App\Livewire\RegisterForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Uuid;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $user = auth()->user();

    $chat = $user->chats()->create([
        'uuid' => Uuid::uuid4()->toString(),
        'name' => fake()->company,
    ]);

    return redirect(route('chat', [
        'chat' => $chat->uuid,
    ]));
})
    ->name('home')
    ->middleware('auth:sanctum');

// =============================================================================
// Auth
// =============================================================================

Route::get('login', LoginForm::class)->name('login');
Route::get('logout', function () {
    Auth::logout();
    return redirect(route('login'));
})->name('logout');
Route::get('register', RegisterForm::class)->name('register');

// =============================================================================
// Chat
// =============================================================================

Route::get('/chat/{chat}', LlmClient::class)
    ->middleware('auth:sanctum')
    ->name('chat');

// =============================================================================
// Library
// =============================================================================

Route::get('/library', Library::class)
    ->middleware('auth:sanctum')
    ->name('library');
