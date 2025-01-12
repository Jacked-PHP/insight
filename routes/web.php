<?php

use App\Livewire\Library;
use App\Livewire\LibraryAsset;
use App\Livewire\LoginForm;
use App\Livewire\LlmClient;
use App\Livewire\RegisterForm;
use App\Livewire\RemoteLibrary;
use App\Livewire\Vault;
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
    return redirect(route('remote-library'));
})
    ->name('home')
    ->middleware('auth:sanctum');

Route::get('create-chat', function () {
    $user = auth()->user();

    $chat = $user->chats()->create([
        'uuid' => Uuid::uuid4()->toString(),
        'name' => fake()->company,
    ]);

    return redirect(route('chat', [
        'chat' => $chat->uuid,
    ]));
})
    ->name('new-chat')
    ->middleware('auth:sanctum');

// =============================================================================
// Auth
// =============================================================================

// Route::get('login', LoginForm::class)->name('login');
Route::get('login', function() {
    auth()->loginUsingId(1);
    return redirect(route('home'));
})->name('login');
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
// Remote Library
// =============================================================================

Route::get('/remote-library', RemoteLibrary::class)
    ->middleware('auth:sanctum')
    ->name('remote-library');
Route::get('/vault/{vault}', Vault::class)
    ->middleware('auth:sanctum')
    ->name('vault');

// =============================================================================
// Library
// =============================================================================

Route::get('/library', Library::class)
    ->middleware('auth:sanctum')
    ->name('library');
Route::get('/library/{asset}', LibraryAsset::class)
    ->middleware('auth:sanctum')
    ->name('library-asset');
