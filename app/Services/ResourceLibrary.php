<?php

namespace App\Services;

use App\Models\Embedding;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ResourceLibrary
{
    public function getEmbeddings(string $text): array
    {
        $result = Ollama::model(config('ollama-laravel.embedding_model'))
            ->embeddings($text);

        return Arr::get($result, 'embedding', []);
    }

    public function indexToken(string $token): Embedding
    {
        return Embedding::create([
            'embedding' => $this->getEmbeddings($token),
            'text' => $token,
        ]);
    }

    public function indexDocument(string $content): Collection
    {
       return collect(explode(PHP_EOL, $content))
           ->filter(fn (string $line) => !empty(trim($line)))
           ->map(fn (string $line) => $this->indexToken($line));
    }

    public function search(
        string $query,
        string $operator = '<=>',
        float $threshold = 0.5,
    ): array {
        $vectors = json_encode($this->getEmbeddings($query));
        return DB::select(<<<SQL
SELECT 
    id,
    text,
    embedding $operator '$vectors' as distance
FROM embeddings
WHERE embedding $operator '$vectors' < $threshold
ORDER BY distance
LIMIT 5;
SQL);
    }
}
