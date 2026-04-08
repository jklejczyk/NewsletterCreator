<?php

namespace App\Domain\Article\Clients;

use App\Domain\Article\Enums\ArticleSource;
use App\Domain\Article\Interfaces\ArticleSourceInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class NewsApiClient implements ArticleSourceInterface
{
    public function __construct(private string $apiKey,
        private string $baseUrl = 'https://newsapi.org/v2/',
        private string $country = 'us',
        private int $pageSize = 20) {}

    /**
     * @return array<int, array<string, string|ArticleSource>>
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function fetch(): array
    {
        $response = Http::get($this->baseUrl.'top-headlines', [
            'apiKey' => $this->apiKey,
            'country' => $this->country,
            'pageSize' => $this->pageSize,
        ])->throw();

        /** @var array<int, array<string, string|ArticleSource>> $articles */
        $articles = $response->json('articles');

        $articles = array_filter($articles, fn (array $article) => !empty($article['content']));

        return array_map(function (array $article) {
            return [
                'title' => $article['title'],
                'content' => $article['content'],
                'url' => $article['url'],
                'source' => ArticleSource::NEWS_API,
                'published_at' => $article['publishedAt'],
            ];
        }, $articles);
    }
}
