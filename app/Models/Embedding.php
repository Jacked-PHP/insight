<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    // use HasFactory;

    protected $connection = 'pgsql';

    protected $table = 'embeddings';

    protected $fillable = [
        'embedding',
        'text',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];
}
