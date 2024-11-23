<?php

namespace App\Enums\Meta;

enum AssetMetadata: string
{
    case CONTENT = 'content';
    case PDF_DATA = 'pdf-data'; // pdf exclusive
}
