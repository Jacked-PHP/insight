<?php

namespace App\Jobs;

use App\Data\PdfTextExtractData;
use App\Enums\AssetType;
use App\Enums\Meta\AssetMetadata;
use App\Events\DocumentContentExtracted;
use App\Events\DocumentContentExtractionFailed;
use App\Models\Asset;
use App\Services\PdfService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExtractDocumentContent implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $assetId
    ) {}

    public function handle(): void
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
        } else {
            report(new Exception('Failed to extract content for asset type: ' . $asset->type . '. Asset ID: ' . $this->assetId));
            event(new DocumentContentExtractionFailed($this->assetId));
            return;
        }

        event(new DocumentContentExtracted($this->assetId));
    }
}
