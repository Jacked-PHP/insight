<?php

namespace App\Services;

use App\Data\PdfPageData;
use App\Data\PdfTextExtractData;
use Illuminate\Support\Facades\File;
use mikehaertl\pdftk\Pdf;
use Vaites\ApacheTika\Client;
use Vaites\ApacheTika\Metadata\MetadataInterface;

class PdfService
{
    public function __construct(
        protected Client $client
    ) {}

    public function extractText(string $path): PdfTextExtractData
    {
        return new PdfTextExtractData(
            path: $path,
            text: $this->client->getText($path),
            pages: $this->extractTextPerPage($path),
            metadata: (array) $this->getMetadata($path),
        );
    }

    public function extractTextPerPage(string $path): array
    {
        $texts = [];

        foreach ($this->split($path) as $index => $page) {
            $text = $this->client->getText($page);
            $texts[] = new PdfPageData(
                page: $index + 1,
                text: $text,
                path: $page,
            );
        }

        return $texts;
    }

    public function split(string $path): array
    {
        $pdf = new Pdf($path);

        $filename = pathinfo($path, PATHINFO_FILENAME);
        $tempPath = storage_path('app/public/temp-' . $filename . '/');
        if (!file_exists($tempPath)) mkdir($tempPath);
        $pdf->burst($tempPath . 'page_%d.pdf');

        $pages = [];
        foreach (glob($tempPath . 'page_*.pdf') as $page) {
            $newPath = storage_path('app/public/library/' . $filename . '-' . basename($page));
            rename($page, $newPath);
            $pages[] = $newPath;
        }

        File::deleteDirectory($tempPath);

        return $pages;
    }

    public function merge(string $output, array $files): void
    {
        $publicPath = storage_path('app/public/');

        $pdf = new Pdf();
        foreach ($files as $file) {
            $pdf->addFile($file);
        }

        $pdf->saveAs($publicPath . $output);
    }

    /**
     * E.g.: (int) $this->getMetadata($path, 'pages');
     */
    public function getMetadata(string $path, ?string $info = null): MetadataInterface|string|null
    {
        $metadata = $this->client->getMetadata($path);

        if ($info === null) {
            return $metadata;
        }

        if (isset($metadata->$info)) {
            return $metadata->$info;
        }

        return null;
    }
}
