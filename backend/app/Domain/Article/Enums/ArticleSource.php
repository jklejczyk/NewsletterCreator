<?php

namespace App\Domain\Article\Enums;

enum ArticleSource: string
{
    case NEWS_API = 'newsapi';
    case RSS = 'rss';
}
