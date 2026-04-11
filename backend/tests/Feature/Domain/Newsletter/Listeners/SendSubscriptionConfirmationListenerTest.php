<?php

use App\Domain\Newsletter\Events\SubscriberRegistered;
use App\Domain\Newsletter\Listeners\SendSubscriptionConfirmationListener;
use App\Domain\Newsletter\Models\Subscriber;
use App\Jobs\SendSubscriptionConfirmationJob;
use Illuminate\Support\Facades\Queue;

it('dispatches send subscription confirmation job when subscriber registers', function () {
    Queue::fake();

    $subscriber = Subscriber::factory()->unconfirmed()->create();

    $listener = new SendSubscriptionConfirmationListener;
    $listener->handle(new SubscriberRegistered($subscriber));

    Queue::assertPushed(SendSubscriptionConfirmationJob::class);
});