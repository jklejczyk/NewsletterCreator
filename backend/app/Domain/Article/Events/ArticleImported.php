<?php

namespace App\Domain\Article\Events;

use App\Domain\Article\Models\Article;

readonly class ArticleImported
{
    public function __construct(public Article $article) {}
}
