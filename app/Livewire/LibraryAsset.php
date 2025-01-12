<?php

namespace App\Livewire;

use App\Enums\Meta\AssetMetadata;
use App\Models\Asset;
use App\Services\ResourceLibrary;
use Livewire\Component;

class LibraryAsset extends Component
{
    public int $assetId;

    public function mount()
    {
        $this->assetId = request()->asset;
    }

    public function indexDocument()
    {
        app(ResourceLibrary::class)->indexDocumentByPath(storage_path('app/public/library'));

        $this->dispatch('success', 'Document indexed successfully!');
    }

    public function render()
    {
        $asset = Asset::find($this->assetId);
        $pages = json_decode($asset->getMeta(AssetMetadata::PDF_DATA->value));

        return view('livewire.library-asset', [
            'asset' => $asset,
            'pages' => $pages ? $pages->pages : [],
        ]);
    }
}
