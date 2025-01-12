<?php

namespace App\Models;

use App\Enums\AssetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Metable\Metable;

class Asset extends Model
{
    use HasFactory;
    use Metable;

    protected $table = 'assets';

    protected $fillable = [
        'uuid',
        'name',
        'path',
        'type',
        'size',
        'vault_id',
        'user_id',
        'embedding_id',
    ];

    protected $casts = [
        'uuid' => 'string',
        'name' => 'string',
        'path' => 'string',
        'type' => AssetType::class,
        'size' => 'integer',
        'vault_id' => 'integer',
        'user_id' => 'integer',
        'embedding_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    public function scopeByPath($query, string $path)
    {
        return $query->where('path', $path);
    }

    public function scopeEmbedded($query)
    {
        return $query->whereNotNull('embedding_id');
    }

    public function scopeNotEmbedded($query)
    {
        return $query->whereNull('embedding_id');
    }
}
