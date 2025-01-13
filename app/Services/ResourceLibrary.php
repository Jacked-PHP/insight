<?php

namespace App\Services;

use App\Models\Embedding;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingFormatter\EmbeddingFormatter;
use LLPhant\Embeddings\EmbeddingGenerator\Ollama\OllamaEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Doctrine\DoctrineVectorStore;
use LLPhant\Embeddings\VectorStores\FileSystem\FileSystemVectorStore;
use LLPhant\OllamaConfig;
use Origin\Text\Text;

class ResourceLibrary
{
    public function getEmbeddings(string $text): array
    {
        $ollamaConfig = new OllamaConfig;
        $ollamaConfig->model = config('ollama-laravel.embedding_model');
        $ollamaConfig->url = config('ollama-laravel.url') . '/api/';
        $embeddingGenerator = new OllamaEmbeddingGenerator($ollamaConfig);
        $embeddedDocuments = $embeddingGenerator->embedText($text);

        return $embeddedDocuments;
    }

    public function indexToken(string $token, ?array $embedding = null): Embedding
    {
        return Embedding::create([
            'embedding' => $embedding ?? $this->getEmbeddings($token),
            'text' => $token,
        ]);
    }

    public function indexDocumentByPath(string $path): void
    {
        $reader = new FileDataReader($path);
        $documents = $reader->getDocuments();
        $splitDocuments = DocumentSplitter::splitDocuments($documents, 800);
        $formattedDocuments = EmbeddingFormatter::formatEmbeddings($splitDocuments);

        $embeddingGenerator = new OllamaEmbeddingGenerator(FileVectorStore::getConfig());
        $embeddedDocuments = $embeddingGenerator->embedDocuments($formattedDocuments);

        $vectorStore = FileVectorStore::getFileStore();
        $vectorStore->addDocuments($embeddedDocuments);
    }

    /**
     * @deprecated
     */
    public function indexDocument(string $content): Collection
    {
        return collect(Text::tokenize($content, [
            'separator' => PHP_EOL,
        ]))
            ->filter(fn(string $line) => !empty(trim($line)))
            ->map(fn(string $line) => $this->indexToken($line));
    }

    public function search(
        string $query,
        string $operator = '<=>',
        float  $threshold = 0.5,
    ): array
    {
        $vectors = json_encode($this->getEmbeddings($query));
        return DB::connection('pgsql')->select(<<<SQL
SELECT
    id,
    text,
    embedding $operator '$vectors' as distance
FROM embeddings
WHERE embedding $operator '$vectors' < $threshold
ORDER BY distance
LIMIT 5;
SQL
        );
    }

    /**
     * @return array<Document>
     */
    public function searchFile(string $query): array
    {
        $vectorStore = new FileSystemVectorStore(config('ollama-laravel.file-store.path'));

        return $vectorStore->similaritySearch($this->getEmbeddings($query), 2);
    }
}
