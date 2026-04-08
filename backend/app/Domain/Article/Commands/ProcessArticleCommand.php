<?php

namespace App\Domain\Article\Commands;

use App\Domain\Article\Models\Article;

readonly class ProcessArticleCommand
{
    public function __construct(public Article $article) {}
}
