<?php

namespace App\Livewire;

use App\Data\PdfTextExtractData;
use App\Enums\AssetType;
use App\Enums\Meta\AssetMetadata;
use App\Models\Asset;
use App\Services\PdfService;
use Livewire\Component;

class LibraryAsset extends Component
{
    public int $assetId;

    public function mount()
    {
        $this->assetId = request()->asset;
    }

    public function extractContent()
    {
        $asset = Asset::find($this->assetId);
        $path = storage_path('app/public/' . $asset->path);

        if ($asset->type === AssetType::PDF) {
            /** @var PdfTextExtractData $content */
            $extractionData = app(PdfService::class)->extractText($path);
            $asset->setMeta(AssetMetadata::CONTENT->value, $extractionData->text);
            $asset->setMeta(AssetMetadata::PDF_DATA->value, json_encode([
                'pages' => $extractionData->pages,
                'metadata' => $extractionData->metadata,
            ]));
        } elseif ($asset->type === AssetType::TEXT) {
            $asset->setMeta(AssetMetadata::CONTENT->value, file_get_contents($path));
        }

        $this->dispatch('error', 'Unsupported file type');
    }

    public function render()
    {
        $asset = Asset::find($this->assetId);
        $pages = json_decode($asset->getMeta(AssetMetadata::PDF_DATA->value));
        // $path = storage_path('app/public/' . $asset->path);
        // app(PdfService::class)->getMetadata($path);

        return view('livewire.library-asset', [
            'asset' => $asset,
            'pages' => $pages ? $pages->pages : [],
        ]);
    }
}
