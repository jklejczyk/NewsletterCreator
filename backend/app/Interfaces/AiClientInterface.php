<?php

namespace App\Interfaces;

use App\Domain\Article\Enums\ArticleCategory;

interface AiClientInterface
{
    public function summarize(string $content): string;
    public function categorize(string $content): ArticleCategory;
}
