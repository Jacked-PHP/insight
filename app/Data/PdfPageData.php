<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class PdfPageData extends Data
{
    public function __construct(
        public int $page,
        public string $text,
        public string $path,
    ) {}
}
