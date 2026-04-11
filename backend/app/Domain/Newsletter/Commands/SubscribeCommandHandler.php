<?php

namespace App\Domain\Newsletter\Commands;

use App\Domain\Newsletter\Events\SubscriberRegistered;
use App\Domain\Newsletter\Models\Subscriber;
use Illuminate\Support\Str;

class SubscribeCommandHandler
{
    public function handle(SubscribeCommand $command): void
    {
        $subscriber = Subscriber::create([
            'email' => $command->email,
            'name' => $command->name,
            'preferences' => $command->preferences,
            'is_active' => false,
            'confirmation_token' => Str::random(64),
            'confirmation_sent_at' => now(),
        ]);

        event(new SubscriberRegistered($subscriber));
    }
}
