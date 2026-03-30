<?php

namespace Database\Seeders;

use App\Domain\Article\Models\Article;
use App\Domain\Newsletter\Models\Newsletter;
use App\Domain\Newsletter\Models\NewsletterSend;
use App\Domain\Newsletter\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('demo123')],
        );

        Article::factory(20)->create();
        Article::factory(10)->processed()->create();

        $subscribers = Subscriber::factory(15)->create();
        Subscriber::factory(3)->unconfirmed()->create();

        $newsletter = Newsletter::factory()->sent()->create();

        $subscribers->each(function ($subscriber) use ($newsletter) {
            NewsletterSend::factory()->create([
                'newsletter_id' => $newsletter->id,
                'subscriber_id' => $subscriber->id,
            ]);
        });

        $newsletter->recipient_count = $newsletter->sends->count();
        $newsletter->save();

        Newsletter::factory(2)->create();
    }
}
