<?php

namespace App\Enums;

enum AssetType: string
{
    case PDF = 'application/pdf';

    /**
     * This covers .txt and .md files.
     */
    case TEXT = 'text/plain';
}
