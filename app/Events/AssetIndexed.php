<?php

namespace App\Events;

use App\Models\Asset;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssetIndexed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $assetId,
        public string $name,
    ) {}

    /**
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('nativephp'),
        ];
    }

    public function getAsset(): Asset
    {
        return Asset::query()->findOrFail($this->assetId);
    }
}
