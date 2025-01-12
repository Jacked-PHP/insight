<?php

namespace App\Livewire;

use App\Enums\Meta\AssetMetadata;
use App\Jobs\IndexVault;
use App\Models\Asset;
use App\Models\Vault;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Native\Laravel\Dialog;

class RemoteLibrary extends Component
{
    public string $path = '';

    public function addVault()
    {
        auth()->user()->vaults()->create(
            $this->only(['path'])
        );

        $this->path = '';

        session()->flash('status', 'Vault successfully created.');
    }

    public function openFolderDialog()
    {
        $newPath = Dialog::new()
            ->folders()
            ->open();

        if ($newPath === null) {
            return;
        }

        $this->path = $newPath;
    }

    public function deleteVault(int $vaultId)
    {
        DB::transaction(function () use ($vaultId) {
            $vault = Vault::find($vaultId);
            $vault->assets->each(function (Asset $asset) {
                $asset->removeManyMeta([
                    AssetMetadata::PDF_DATA->value,
                    AssetMetadata::CONTENT->value,
                ]);
            });
            $vault->assets()->delete();
            $vault->delete();
        });

        session()->flash('status', 'Vault successfully deleted.');
    }

    public function render()
    {
        $vaults = Vault::all();

        return view('livewire.remote-library', [
            'vaults' => $vaults,
        ]);
    }
}
