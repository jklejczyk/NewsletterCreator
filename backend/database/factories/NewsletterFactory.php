<?php

namespace Database\Factories;

use App\Domain\Newsletter\Enums\NewsletterStatus;
use App\Domain\Newsletter\Models\Newsletter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Newsletter>
 */
class NewsletterFactory extends Factory
{
    protected $model = Newsletter::class;

    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(),
            'content' => fake()->paragraphs(5, true),
            'sent_at' => null,
            'recipient_count' => 0,
            'status' => NewsletterStatus::DRAFT,
        ];
    }

    public function sent(): static
    {
        return $this->state([
            'sent_at' => now(),
            'recipient_count' => fake()->numberBetween(10, 500),
            'status' => NewsletterStatus::SENT,
        ]);
    }
}
