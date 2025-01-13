<?php

namespace App\Services;

use Illuminate\Support\Collection;
use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\VectorStores\FileSystem\FileSystemVectorStore;
use LLPhant\OllamaConfig;

class FileVectorStore extends FileSystemVectorStore
{
    public static function getConfig(): OllamaConfig
    {
        $ollamaConfig = new OllamaConfig;
        $ollamaConfig->stream = true;
        $ollamaConfig->model = config('ollama-laravel.embedding_model');
        $ollamaConfig->url = config('ollama-laravel.url') . '/api/';

        return $ollamaConfig;
    }

    public static function getFileStore(): FileSystemVectorStore
    {
        return new self(config('ollama-laravel.file-store.path'));
    }

    public function removeDocumentFromFile(string $path): void
    {
        if (!file_exists($this->filePath)) {
            return;
        }

        $documents = $this
            ->getAllDocumentsFromFile()
            ->filter(function ($document) use ($path) {
                return $document->sourceName === $path;
            });

        unlink($this->filePath);

        $this->addDocuments($documents->toArray());
    }

    private function getAllDocumentsFromFile(): Collection
    {
        // Check if file exists and we can open it
        if (! is_readable($this->filePath)) {
            return collect();
        }

        // Get the JSON data from the file
        $jsonData = file_get_contents($this->filePath);
        if ($jsonData === false) {
            return collect();
        }

        // Decode the JSON data into an array
        $data = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($data)) {
            return collect();
        }

        // Convert each associative array entry into a Document object
        return collect($data)->map(function (array $entry): Document {
            $document = new Document();
            $document->content = $entry['content'] ?? '';
            $document->formattedContent = $entry['formattedContent'] ?? null;
            $document->embedding = $entry['embedding'] ?? null;
            $document->sourceType = $entry['sourceType'] ?? null;
            $document->sourceName = $entry['sourceName'] ?? null;
            $document->hash = $entry['hash'] ?? null;

            return $document;
        });
    }
}
