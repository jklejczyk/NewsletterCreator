<?php

namespace App\Domain\Article\Query;

use App\Domain\Article\Enums\ArticleCategory;

final readonly class GetArticlesQuery
{
    public function __construct(public ?ArticleCategory $category = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public int $perPage = 15) {}
}
