<?php

namespace App\Domain\Article\Interfaces;

use App\Domain\Article\Enums\ArticleSource;

interface ArticleSourceInterface
{
    /** @return array<int, array<string, string|ArticleSource>> */
    public function fetch(): array;
}
