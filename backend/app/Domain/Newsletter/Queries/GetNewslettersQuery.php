<?php

namespace App\Domain\Newsletter\Queries;

use App\Domain\Newsletter\Enums\NewsletterStatus;

final readonly class GetNewslettersQuery
{
    public function __construct(
        public ?NewsletterStatus $status = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public int $perPage = 20,
    ) {}
}
