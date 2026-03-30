<?php

namespace App\Domain\Article\Enums;

enum ArticleCategory: string
{
    case TECHNOLOGY = 'technology';
    case BUSINESS   = 'business';
    case SCIENCE    = 'science';
    case OTHER      = 'other';
}
