<?php

namespace App\Jobs;

use App\Domain\Article\Models\Article;
use App\Domain\Newsletter\Models\Newsletter;
use App\Domain\Newsletter\Models\NewsletterSend;
use App\Domain\Newsletter\Models\Subscriber;
use App\Mail\PersonalizedNewsletterMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPersonalizedNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    /**
     * @param  Collection<int, Article>  $articles
     */
    public function __construct(private Newsletter $newsletter, private Subscriber $subscriber, private Collection $articles) {}

    public function handle(): void
    {
        $filteredArticles = $this->articles->whereIn('category', $this->subscriber->preferences ?? []);

        if (! $filteredArticles->count()) {
            return;
        }

        Mail::to($this->subscriber->email)->send(new PersonalizedNewsletterMail($this->newsletter, $this->subscriber, $filteredArticles));

        NewsletterSend::create([
            'newsletter_id' => $this->newsletter->id,
            'subscriber_id' => $this->subscriber->id,
            'sent_at' => now(),
        ]);

        $this->newsletter->increment('recipient_count');
    }
}
