<?php

namespace Database\Factories;

use App\Domain\Newsletter\Models\Newsletter;
use App\Domain\Newsletter\Models\NewsletterSend;
use App\Domain\Newsletter\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsletterSend>
 */
class NewsletterSendFactory extends Factory
{
    protected $model = NewsletterSend::class;

    public function definition(): array
    {
        return [
            'newsletter_id' => Newsletter::factory(),
            'subscriber_id' => Subscriber::factory(),
            'sent_at' => now(),
            'opened_at' => fake()->optional(0.3)->dateTimeBetween('-1 week'),
        ];
    }
}
