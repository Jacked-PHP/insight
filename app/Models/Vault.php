<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Metable\Metable;

class Vault extends Model
{
    use Metable;

    protected $fillable = [
        'path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
