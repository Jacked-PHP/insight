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
            ->notEmbedded()
            ->first();

        if ($asset === null) {
            return;
        }

        $asset->embedding_id = app(ResourceLibrary::class)
            ->indexDocumentByPath($asset->path)
            ->first()
            ->id; // always returns a collection of one.

        $asset->save();

        event(new AssetIndexed(
            assetId: $asset->id,
            name: $asset->name,
        ));

        logger()->info($asset->path . ' (' . $asset->id . ') indexed!');
    }
}
