<?php

namespace App\Jobs;

use App\Events\AssetIndexed;
use App\Models\Asset;
use App\Services\ResourceLibrary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IndexVaultItem implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $assetId
    ) {}

    public function handle(): void
    {
        $asset = Asset::query()
            ->where('id', $this->assetId)
            ->notIndexed()
            ->first();

        if ($asset === null) {
            return;
        }

        app(ResourceLibrary::class)->indexDocumentByPath($asset->path);

        event(new AssetIndexed(
            assetId: $asset->id,
            name: $asset->name,
        ));

        logger()->info($asset->path . ' (' . $asset->id . ') indexed!');
    }
}
