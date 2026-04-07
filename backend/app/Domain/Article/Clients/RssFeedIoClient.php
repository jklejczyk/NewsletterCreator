<?php

namespace App\Domain\Article\Clients;

use App\Domain\Article\Enums\ArticleSource;
use App\Domain\Article\Interfaces\ArticleSourceInterface;
use FeedIo\FeedIo;

class RssFeedIoClient implements ArticleSourceInterface
{
    /** @param array<int, string> $feeds */
    public function __construct(private FeedIo $feedIo,
                                private array  $feeds)
    {
    }

    /** @return array<int, array<string, string|ArticleSource>> */
    public function fetch(): array
    {
        $articles = [];

        foreach ($this->feeds as $feedUrl) {
            $result = $this->feedIo->read($feedUrl);

            foreach ($result->getFeed() as $item) {
                $articles[] = [
                    'title' => $item->getTitle(),
                    'content' => $item->getContent(),
                    'url' => $item->getLink(),
                    'source' => ArticleSource::RSS,
                    'published_at' => $item->getLastModified()->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $articles;
    }
}

