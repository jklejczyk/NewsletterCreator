<?php

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Article\Models\Article;
use App\Domain\Newsletter\Commands\CreateNewsletterCommand;
use App\Domain\Newsletter\Commands\CreateNewsletterCommandHandler;
use App\Domain\Newsletter\Enums\NewsletterStatus;
use App\Domain\Newsletter\Events\NewsletterCreated;
use App\Domain\Newsletter\Models\Newsletter;
use Illuminate\Support\Facades\Event;

it('creates newsletter from processed articles and emits event', function () {
    Event::fake();

    Article::factory()->processed()->create([
        'summary' => 'Test summary',
        'category' => ArticleCategory::TECHNOLOGY,
        'imported_at' => now(),
    ]);

    $handler = new CreateNewsletterCommandHandler;
    $handler->handle(new CreateNewsletterCommand);

    expect(Newsletter::count())->toBe(1)
        ->and(Newsletter::first()->status)->toBe(NewsletterStatus::DRAFT)
        ->and(Newsletter::first()->recipient_count)->toBe(0)
        ->and(Newsletter::first()->content)->toContain('Test summary');

    Event::assertDispatched(NewsletterCreated::class);
});

it('does not create newsletter when no processed articles exist', function () {
    Event::fake();

    Article::factory()->create([
        'is_processed' => false,
        'imported_at' => now(),
    ]);

    $handler = new CreateNewsletterCommandHandler;
    $handler->handle(new CreateNewsletterCommand);

    expect(Newsletter::count())->toBe(0);

    Event::assertNotDispatched(NewsletterCreated::class);
});

it('ignores articles older than 24 hours', function () {
    Event::fake();

    Article::factory()->processed()->create([
        'summary' => 'Old article',
        'imported_at' => now()->subHours(25),
    ]);

    $handler = new CreateNewsletterCommandHandler;
    $handler->handle(new CreateNewsletterCommand);

    expect(Newsletter::count())->toBe(0);

    Event::assertNotDispatched(NewsletterCreated::class);
});
