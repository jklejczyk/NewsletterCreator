<?php

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Article\Models\Article;
use App\Domain\Article\Query\GetArticlesQuery;
use App\Domain\Article\Query\GetArticlesQueryHandler;

it('returns only processed articles', function () {
    Article::factory()->processed()->create(['imported_at' => now()]);
    Article::factory()->create(['is_processed' => false, 'imported_at' => now()]);

    $handler = new GetArticlesQueryHandler;
    $result = $handler->handle(new GetArticlesQuery);

    expect($result->total())->toBe(1);
});

it('filters articles by category', function () {
    Article::factory()->processed()->create([
        'category' => ArticleCategory::TECHNOLOGY,
        'imported_at' => now(),
    ]);

    Article::factory()->processed()->create([
        'category' => ArticleCategory::SCIENCE,
        'imported_at' => now(),
    ]);

    $handler = new GetArticlesQueryHandler;
    $result = $handler->handle(new GetArticlesQuery(
        category: ArticleCategory::TECHNOLOGY,
    ));

    expect($result->total())->toBe(1)
        ->and($result->items()[0]->category)->toBe(ArticleCategory::TECHNOLOGY);
});

it('filters articles by date range', function () {
    Article::factory()->processed()->create([
        'published_at' => '2026-04-10',
        'imported_at' => now(),
    ]);

    Article::factory()->processed()->create([
        'published_at' => '2026-04-01',
        'imported_at' => now(),
    ]);

    $handler = new GetArticlesQueryHandler;
    $result = $handler->handle(new GetArticlesQuery(
        dateFrom: '2026-04-05',
        dateTo: '2026-04-12',
    ));

    expect($result->total())->toBe(1);
});

it('paginates results with given per page', function () {
    Article::factory()->processed()->count(25)->create(['imported_at' => now()]);

    $handler = new GetArticlesQueryHandler;
    $result = $handler->handle(new GetArticlesQuery(perPage: 10));

    expect($result->perPage())->toBe(10)
        ->and($result->total())->toBe(25)
        ->and($result->count())->toBe(10);
});
