<?php

namespace App\Livewire;

use App\Enums\AssetType;
use App\Enums\Meta\AssetMetadata;
use App\Jobs\IndexVaultItem;
use App\Models\Asset;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\Vault as VaultModel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Vault extends Component
{
    public int $vaultId;

    public function mount(int $vault)
    {
        $this->vaultId = $vault;
    }

    public function loadVaultsFiles()
    {
        $vault = VaultModel::find($this->vaultId);

        /** @var RecursiveIteratorIterator $files */
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($vault->path));

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $asset = Asset::byPath($file->getRealPath())->first();
                if ($asset === null) {
                    $asset = Asset::create([
                        'uuid' => Str::uuid()->toString(),
                        'name' => $file->getFilename(),
                        'path' => $file->getRealPath(),
                        'type' => AssetType::TEXT,
                        'size' => $file->getSize(),
                        'vault_id' => $this->vaultId,
                        'user_id' => auth()->id(),
                    ]);
                }
                $asset->setMeta(AssetMetadata::CONTENT->value, file_get_contents($file->getRealPath()));
            }
        }
    }

    public function indexAssets()
    {
        foreach (VaultModel::find($this->vaultId)->assets as $asset) {
            IndexVaultItem::dispatch($asset->id);
        }

        session()->flash('status', 'Vault index triggered!');
    }

    public function indexMissingAssets()
    {
        foreach (VaultModel::find($this->vaultId)->assets()->whereNull('embedding_id') as $asset) {
            IndexVaultItem::dispatch($asset->id);
        }

        session()->flash('status', 'Vault index triggered!');
    }

    public function render()
    {
        $vault = VaultModel::find($this->vaultId);

        return view('livewire.vault', [
            'vault' => $vault,
            'assets' => $vault->assets,
        ]);
    }
}
