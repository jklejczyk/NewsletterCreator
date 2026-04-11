<?php

namespace App\Domain\Newsletter\Listeners;

use App\Domain\Newsletter\Events\SubscriberRegistered;
use App\Jobs\SendSubscriptionConfirmationJob;

class SendSubscriptionConfirmationListener
{
    public function handle(SubscriberRegistered $event): void
    {
        SendSubscriptionConfirmationJob::dispatch($event->subscriber);
    }
}
