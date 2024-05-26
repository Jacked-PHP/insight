<?php

namespace App\Livewire;

use Livewire\Component;

class UserMenu extends Component
{
    public function updateChatName(string $chatUuid, string $name)
    {
        auth()->user()->chats()->where('uuid', $chatUuid)->firstOrFail()->update([
            'name' => $name,
        ]);
    }

    public function render()
    {
        return view('livewire.user-menu');
    }
}
