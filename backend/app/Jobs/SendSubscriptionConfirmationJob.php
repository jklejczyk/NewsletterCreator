<?php

namespace App\Jobs;

use App\Domain\Newsletter\Models\Subscriber;
use App\Mail\SubscriptionConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(private Subscriber $subscriber) {}

    public function handle(): void
    {
        Mail::to($this->subscriber->email)->send(new SubscriptionConfirmationMail($this->subscriber));
    }
}
