<?php

use App\Domain\Article\Clients\NewsApiClient;
use App\Domain\Article\Enums\ArticleSource;
use Illuminate\Support\Facades\Http;

it('fetches articles from news api', function () {
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
                    'source' => ['id' => 'test', 'name' =>
                        'Test'],
                    'author' => 'Test Author',
                    'description' => 'Test description',
                    'urlToImage' => null,
                ],
            ],
        ]),
    ]);

    $client = new NewsApiClient('fake-key');
    $articles = $client->fetch();

    expect($articles)->toHaveCount(1)
        ->and($articles[0]['title'])->toBe('Test Article')
        ->and($articles[0]['content'])->toBe('Test content')
        ->and($articles[0]['source'])->toBe(ArticleSource::NEWS_API);
});

it('throws exception on failed response', function () {
    Http::fake(['https://newsapi.org/v2/top-headlines*' => Http::response([], 500)]);

    $client = new NewsApiClient('fake-key');
    $client->fetch();
})->throws(Illuminate\Http\Client\RequestException::class);

it('throws exception on when api key is invalid', function () {
    Http::fake(['https://newsapi.org/v2/top-headlines*' => Http::response([
        'status' => 'error',
        'code' => 'apiKeyInvalid',
    ], 401)]);

    $client = new NewsApiClient('fake-key');
    $client->fetch();
})->throws(Illuminate\Http\Client\RequestException::class);
