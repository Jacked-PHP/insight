<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class PdfTextExtractData extends Data
{
    /**
     * @param array<PdfPageData> $pages
     */
    public function __construct(
        public string $path,
        public string $text,
        public array $pages,
        public array $metadata,
    ) {}
}
