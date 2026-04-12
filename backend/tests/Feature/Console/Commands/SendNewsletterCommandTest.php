<?php

use App\Domain\Article\Models\Article;
use App\Domain\Newsletter\Events\NewsletterCreated;
use Illuminate\Support\Facades\Event;

it('dispatches newsletter creation and outputs success message', function () {
    Event::fake();

    Article::factory()->processed()->create(['imported_at' => now()]);

    $this->artisan('newsletter:send')
        ->assertSuccessful()
        ->expectsOutputToContain('Newsletter dispatch initiated.');

    Event::assertDispatched(NewsletterCreated::class);
});
