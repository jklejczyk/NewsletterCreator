<?php

use App\Domain\Newsletter\Enums\NewsletterStatus;
use App\Domain\Newsletter\Models\Newsletter;
use App\Domain\Newsletter\Queries\GetNewslettersQuery;
use App\Domain\Newsletter\Queries\GetNewslettersQueryHandler;

it('returns all newsletters paginated', function () {
    Newsletter::factory()->count(5)->create();

    $handler = new GetNewslettersQueryHandler;
    $result = $handler->handle(new GetNewslettersQuery);

    expect($result->total())->toBe(5);
});

it('filters newsletters by status', function () {
    Newsletter::factory()->create(['status' => NewsletterStatus::SENT]);
    Newsletter::factory()->create(['status' => NewsletterStatus::DRAFT]);
    Newsletter::factory()->create(['status' => NewsletterStatus::SENDING]);

    $handler = new GetNewslettersQueryHandler;
    $result = $handler->handle(new GetNewslettersQuery(
        status: NewsletterStatus::SENT,
    ));

    expect($result->total())->toBe(1)
        ->and($result->items()[0]->status)->toBe(NewsletterStatus::SENT);
});

it('filters newsletters by date range', function () {
    Newsletter::factory()->create(['sent_at' => '2026-04-10']);
    Newsletter::factory()->create(['sent_at' => '2026-04-01']);
    Newsletter::factory()->create(['sent_at' => null]);

    $handler = new GetNewslettersQueryHandler;
    $result = $handler->handle(new GetNewslettersQuery(
        dateFrom: '2026-04-05',
        dateTo: '2026-04-12',
    ));

    expect($result->total())->toBe(1);
});

it('paginates results with given per page', function () {
    Newsletter::factory()->count(15)->create();

    $handler = new GetNewslettersQueryHandler;
    $result = $handler->handle(new GetNewslettersQuery(perPage: 5));

    expect($result->perPage())->toBe(5)
        ->and($result->total())->toBe(15)
        ->and($result->count())->toBe(5);
});
