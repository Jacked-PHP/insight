<?php

namespace App\Models;

use App\Enums\AssetType;
use Illuminate\Database\Eloquent\Builder;
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
        'indexed_at',
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
        'indexed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    public function scopeByPath(Builder $query, string $path): void
    {
        $query->where('path', $path);
    }

    public function scopeIndexed(Builder $query): void
    {
        $query->whereNotNull('indexed_at');
    }

    public function scopeNotIndexed(Builder $query): void
    {
        $query->whereNull('indexed_at');
    }
}
