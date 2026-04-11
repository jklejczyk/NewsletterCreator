<?php

use App\Domain\Newsletter\Models\Subscriber;
use App\Jobs\SendSubscriptionConfirmationJob;
use App\Mail\SubscriptionConfirmationMail;
use Illuminate\Support\Facades\Mail;

it('sends subscription confirmation mail to subscriber email', function () {
    Mail::fake();

    $subscriber = Subscriber::factory()->unconfirmed()->create([
        'email' => 'test@example.com',
        'confirmation_token' => str_repeat('a', 64),
    ]);

    (new SendSubscriptionConfirmationJob($subscriber))->handle();

    Mail::assertSent(
        SubscriptionConfirmationMail::class,
        fn (SubscriptionConfirmationMail $mail) =>
            $mail->hasTo('test@example.com') && $mail->subscriber->is($subscriber)
    );
});
