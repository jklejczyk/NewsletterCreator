<?php

use App\Domain\Article\Clients\NewsApiClient;
use App\Domain\Article\Commands\ImportArticlesCommand;
use App\Domain\Article\Commands\ImportArticlesCommandHandler;
use App\Domain\Article\Events\ArticleImported;
use App\Domain\Article\Models\Article;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

it('imports articles and fires event', function () {
    Event::fake();

    Http::fake([
        'https://newsapi.org/v2/top-headlines*' => Http::response([
            'status' => 'ok',
            'totalResults' => 1,
            'articles' => [
                [
                    'title' => 'Test Article',
                    'content' => 'Test content',
                    'url' => 'https://example.com/article',
                    'publishedAt' => '2026-04-07T10:00:00Z',
                    'source' => [
                        'id' => 'test',
                        'name' => 'Test',
                    ],
                    'author' => 'Author',
                    'description' => 'Desc',
                    'urlToImage' => null,
                ],
            ],
        ]),
    ]);

    $client = new NewsApiClient('fake-key');
    $handler = new ImportArticlesCommandHandler;
    $handler->handle(new ImportArticlesCommand([$client]));

    expect(Article::count())->toBe(1)
        ->and(Article::first()->title)->toBe('Test Article');

    Event::assertDispatched(ArticleImported::class);
});

it('does not create duplicate articles', function () {
    Event::fake();

    Article::factory()->create(['url' => 'https://example.com/article']);

    Http::fake([
        'https://newsapi.org/v2/top-headlines*' => Http::response([
            'status' => 'ok',
            'totalResults' => 1,
            'articles' => [
                [
                    'title' => 'Test Article',
                    'content' => 'Test content',
                    'url' => 'https://example.com/article',
                    'publishedAt' => '2026-04-07T10:00:00Z',
                    'source' => [
                        'id' => 'test',
                        'name' => 'Test',
                    ],
                    'author' => 'Author',
                    'description' => 'Desc',
                    'urlToImage' => null,
                ],
            ],
        ]),
    ]);

    $client = new NewsApiClient('fake-key');
    $handler = new ImportArticlesCommandHandler;
    $handler->handle(new ImportArticlesCommand([$client]));

    expect(Article::count())->toBe(1);
    Event::assertNotDispatched(ArticleImported::class);
});
