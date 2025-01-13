<?php

namespace App\Listeners;

use App\Events\AssetIndexed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MarkAssetAsIndexed
{
    public function handle(AssetIndexed $event): void
    {
        $event->getAsset()->update([
            'indexed_at' => now(),
        ]);
    }
}
