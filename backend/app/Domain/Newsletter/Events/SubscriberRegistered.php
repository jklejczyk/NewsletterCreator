<?php

namespace App\Domain\Newsletter\Events;

use App\Domain\Newsletter\Models\Subscriber;

readonly class SubscriberRegistered
{
    public function __construct(public Subscriber $subscriber) {}
}
