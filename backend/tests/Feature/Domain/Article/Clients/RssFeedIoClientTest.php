<?php

use App\Domain\Article\Clients\RssFeedIoClient;
use App\Domain\Article\Enums\ArticleSource;
use FeedIo\Feed;
use FeedIo\Feed\Item;
use FeedIo\FeedIo;
use FeedIo\Reader\ReadErrorException;
use FeedIo\Reader\Result;

it('fetches articles from rss through feed io', function () {
    $item = new Item;
    $item->setTitle('RSS Article');
    $item->setContent('RSS content');
    $item->setLink('https://example.com/rss-article');
    $item->setLastModified(new DateTime('2026-04-07 10:00:00'));

    $feed = new Feed;
    $feed->add($item);

    $result = Mockery::mock(Result::class);
    $result->shouldReceive('getFeed')->andReturn($feed);

    $feedIo = Mockery::mock(FeedIo::class);
    $feedIo->shouldReceive('read')
        ->with('https://example.com/feed')
        ->andReturn($result);

    $client = new RssFeedIoClient($feedIo, ['https://example.com/feed']);
    $articles = $client->fetch();

    expect($articles)->toHaveCount(1)
        ->and($articles[0]['title'])->toBe('RSS Article')
        ->and($articles[0]['content'])->toBe('RSS content')
        ->and($articles[0]['source'])->toBe(ArticleSource::RSS);
});

it('returns empty array when feed has no items', function () {
    $feed = new Feed;

    $result = Mockery::mock(Result::class);
    $result->shouldReceive('getFeed')->andReturn($feed);

    $feedIo = Mockery::mock(FeedIo::class);
    $feedIo->shouldReceive('read')->andReturn($result);

    $client = new RssFeedIoClient($feedIo, ['https://example.com/feed']);

    expect($client->fetch())->toBeEmpty();
});

it('throws exception when feed is unreachable', function () {
    $feedIo = Mockery::mock(FeedIo::class);
    $feedIo->shouldReceive('read')
        ->andThrow(new ReadErrorException('Invalid url'));

    $client = new RssFeedIoClient($feedIo, ['https://example.com/feed']);
    $client->fetch();
})->throws(ReadErrorException::class);
