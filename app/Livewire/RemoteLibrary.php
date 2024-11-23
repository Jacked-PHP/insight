<?php

namespace App\Livewire;

use App\Models\Vault;
use Livewire\Component;

class RemoteLibrary extends Component
{
    public string $path = '';

    public function addVault()
    {
        auth()->user()->vaults()->create(
            $this->only(['path'])
        );

        session()->flash('status', 'Vault successfully created.');
    }

    public function render()
    {
        $vaults = Vault::all();

        return view('livewire.remote-library', [
            'vaults' => $vaults,
        ]);
    }
}
