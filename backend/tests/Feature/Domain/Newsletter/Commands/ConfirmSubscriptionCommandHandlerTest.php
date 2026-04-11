<?php

use App\Domain\Newsletter\Commands\ConfirmSubscriptionCommand;
use App\Domain\Newsletter\Commands\ConfirmSubscriptionCommandHandler;
use App\Domain\Newsletter\Exceptions\AlreadyConfirmedException;
use App\Domain\Newsletter\Exceptions\ConfirmationTokenExpiredException;
use App\Domain\Newsletter\Exceptions\ConfirmationTokenInvalidException;
use App\Domain\Newsletter\Models\Subscriber;

it('confirms subscriber', function () {
    $subscriber = Subscriber::factory()->unconfirmed()->inactive()->create([
        'confirmation_token' => 'valid-token',
        'confirmation_sent_at' => now(),
    ]);

    $handler = new ConfirmSubscriptionCommandHandler;
    $handler->handle(new ConfirmSubscriptionCommand('valid-token'));

    $subscriber->refresh();

    expect($subscriber->is_active)->toBeTrue()
        ->and($subscriber->confirmed_at)->not->toBeNull();
});

it('throws exception when token does not exist', function () {
    $handler = new ConfirmSubscriptionCommandHandler;
    $handler->handle(new ConfirmSubscriptionCommand('nonexistent-token'));
})->throws(ConfirmationTokenInvalidException::class);

it('throws exception when subscriber is already confirmed', function () {
    Subscriber::factory()->create([
        'confirmation_token' => 'already-used-token',
    ]);

    $handler = new ConfirmSubscriptionCommandHandler;
    $handler->handle(new ConfirmSubscriptionCommand('already-used-token'));
})->throws(AlreadyConfirmedException::class);

it('throws exception when confirmation token has expired', function () {
    $ttlHours = config('newsletter.confirmation_ttl_hours');

    Subscriber::factory()->unconfirmed()->inactive()->create([
        'confirmation_token' => 'expired-token',
        'confirmation_sent_at' => now()->subHours($ttlHours + 1),
    ]);

    $handler = new ConfirmSubscriptionCommandHandler;
    $handler->handle(new ConfirmSubscriptionCommand('expired-token'));
})->throws(ConfirmationTokenExpiredException::class);
