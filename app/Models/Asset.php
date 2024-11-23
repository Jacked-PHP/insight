<?php

namespace App\Models;

use App\Enums\AssetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'user_id',
    ];

    protected $casts = [
        'uuid' => 'string',
        'name' => 'string',
        'path' => 'string',
        'type' => AssetType::class,
        'size' => 'integer',
        'user_id' => 'integer',
    ];
}
