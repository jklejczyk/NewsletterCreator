<?php

use App\Domain\Article\Models\Article;
use App\Domain\Newsletter\Enums\NewsletterStatus;
use App\Domain\Newsletter\Events\NewsletterCreated;
use App\Domain\Newsletter\Listeners\SendNewsletterListener;
use App\Domain\Newsletter\Models\Newsletter;
use App\Domain\Newsletter\Models\Subscriber;
use App\Jobs\SendPersonalizedNewsletterJob;
use Illuminate\Support\Facades\Queue;

it('dispatches job per active subscriber and updates status', function () {
    Queue::fake();

    $newsletter = Newsletter::factory()->create();
    $articles = Article::factory()->processed()->count(2)->create(['imported_at' => now()]);

    Subscriber::factory()->count(3)->create(['is_active' => true]);
    Subscriber::factory()->inactive()->create();

    $listener = new SendNewsletterListener;
    $listener->handle(new NewsletterCreated($newsletter, $articles));

    Queue::assertPushed(SendPersonalizedNewsletterJob::class, 3);

    $newsletter->refresh();

    expect($newsletter->sent_at)->not->toBeNull()
        ->and($newsletter->status)->toBe(NewsletterStatus::SENT);
});

it('does not dispatch jobs when no active subscribers exist', function () {
    Queue::fake();

    $newsletter = Newsletter::factory()->create();
    $articles = Article::factory()->processed()->count(2)->create(['imported_at' => now()]);

    Subscriber::factory()->inactive()->count(2)->create();

    $listener = new SendNewsletterListener;
    $listener->handle(new NewsletterCreated($newsletter, $articles));

    Queue::assertNothingPushed();
});
