<?php

namespace App\Domain\Newsletter\Commands;

use App\Domain\Article\Enums\ArticleCategory;

final readonly class SubscribeCommand
{
    /** @param array<int, ArticleCategory> $preferences */
    public function __construct(
        public string $email,
        public string $name,
        public array $preferences,
    ) {}
}
