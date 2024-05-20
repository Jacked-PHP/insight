<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    use HasFactory;

    protected $table = 'embeddings';

    protected $fillable = [
        'embedding',
        'text',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];
}
