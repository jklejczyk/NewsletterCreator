<?php

namespace App\Domain\Newsletter\Listeners;

use App\Domain\Article\Models\Article;
use App\Domain\Newsletter\Enums\NewsletterStatus;
use App\Domain\Newsletter\Events\NewsletterCreated;
use App\Domain\Newsletter\Models\Subscriber;
use App\Jobs\SendPersonalizedNewsletterJob;
use Illuminate\Database\Eloquent\Collection;

class SendNewsletterListener
{
    /**
     * @param NewsletterCreated $event
     * @return void
     */
    public function handle(NewsletterCreated $event): void
    {
        $event->newsletter->update([
            'status' => NewsletterStatus::SENDING,
            'sent_at' => now(),
        ]);

        try {
            Subscriber::where('is_active', true)->each(function (Subscriber $subscriber) use ($event) {
                SendPersonalizedNewsletterJob::dispatch($event->newsletter, $subscriber, $event->articles);
            });
        } catch (\Exception $exception) {
            $event->newsletter->update([
                'status' => NewsletterStatus::FAILED,
            ]);
        }

        $event->newsletter->update([
            'status' => NewsletterStatus::SENT,
        ]);
    }
}
