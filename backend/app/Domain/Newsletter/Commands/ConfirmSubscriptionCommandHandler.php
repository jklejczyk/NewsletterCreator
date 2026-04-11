<?php

namespace App\Domain\Newsletter\Commands;

use App\Domain\Newsletter\Exceptions\AlreadyConfirmedException;
use App\Domain\Newsletter\Exceptions\ConfirmationTokenExpiredException;
use App\Domain\Newsletter\Exceptions\ConfirmationTokenInvalidException;
use App\Domain\Newsletter\Models\Subscriber;

class ConfirmSubscriptionCommandHandler
{
    public function handle(ConfirmSubscriptionCommand $command): void
    {
        $subscriber = Subscriber::where('confirmation_token', $command->token)->first();

        if (! $subscriber) {
            throw new ConfirmationTokenInvalidException;
        }

        if ($subscriber->is_active) {
            throw new AlreadyConfirmedException;
        }

        if ($subscriber->confirmation_sent_at?->addHours(config('newsletter.confirmation_ttl_hours'))->isPast()) {
            throw new ConfirmationTokenExpiredException;
        }

        $subscriber->is_active = true;
        $subscriber->confirmed_at = now();
        $subscriber->save();
    }
}
