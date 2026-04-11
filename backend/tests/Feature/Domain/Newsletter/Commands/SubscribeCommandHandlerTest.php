<?php

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Newsletter\Commands\SubscribeCommand;
use App\Domain\Newsletter\Commands\SubscribeCommandHandler;
use App\Domain\Newsletter\Events\SubscriberRegistered;
use App\Domain\Newsletter\Models\Subscriber;
use Illuminate\Support\Facades\Event;

it('creates inactive subscriber with confirmation token', function () {
    Event::fake();

    $handler = new SubscribeCommandHandler;
    $handler->handle(new SubscribeCommand(
        email: 'test@example.com',
        name: 'Jan Kowalski',
        preferences: [ArticleCategory::TECHNOLOGY, ArticleCategory::SCIENCE],
    ));

    $subscriber = Subscriber::where('email', 'test@example.com')->first();

    expect($subscriber)->not->toBeNull()
        ->and($subscriber->name)->toBe('Jan Kowalski')
        ->and($subscriber->is_active)->toBeFalse()
        ->and($subscriber->confirmed_at)->toBeNull()
        ->and($subscriber->confirmation_token)->toHaveLength(64)
        ->and($subscriber->confirmation_sent_at)->not->toBeNull();
});

it('stores subscriber preferences as enum collection', function () {
    Event::fake();

    $handler = new SubscribeCommandHandler;
    $handler->handle(new SubscribeCommand(
        email: 'test@example.com',
        name: 'Jan Kowalski',
        preferences: [ArticleCategory::TECHNOLOGY, ArticleCategory::BUSINESS],
    ));

    $subscriber = Subscriber::where('email', 'test@example.com')->first();

    expect($subscriber->preferences->all())->toBe([
        ArticleCategory::TECHNOLOGY,
        ArticleCategory::BUSINESS,
    ]);
});

it('fires subscriber registered event with fresh subscriber', function () {
    Event::fake();

    $handler = new SubscribeCommandHandler;
    $handler->handle(new SubscribeCommand(
        email: 'test@example.com',
        name: 'Jan Kowalski',
        preferences: [ArticleCategory::TECHNOLOGY],
    ));

    Event::assertDispatched(
        SubscriberRegistered::class,
        fn (SubscriberRegistered $event) => $event->subscriber->email === 'test@example.com'
    );
});