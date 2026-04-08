<?php

namespace App\Domain\Article\Commands;

use App\Domain\Article\Interfaces\ArticleSourceInterface;

readonly class ImportArticlesCommand
{
    /** @param array<int, ArticleSourceInterface> $sources */
    public function __construct(public array $sources)
    {}
}
