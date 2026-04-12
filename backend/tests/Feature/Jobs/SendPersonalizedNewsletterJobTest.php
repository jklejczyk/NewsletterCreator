<?php

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Article\Models\Article;
use App\Domain\Newsletter\Models\Newsletter;
use App\Domain\Newsletter\Models\NewsletterSend;
use App\Domain\Newsletter\Models\Subscriber;
use App\Jobs\SendPersonalizedNewsletterJob;
use App\Mail\PersonalizedNewsletterMail;
use Illuminate\Support\Facades\Mail;

it('sends mail with articles matching subscriber preferences', function () {
    Mail::fake();

    $newsletter = Newsletter::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'is_active' => true,
        'preferences' => [ArticleCategory::TECHNOLOGY],
    ]);

    $techArticle = Article::factory()->processed()->create([
        'category' => ArticleCategory::TECHNOLOGY,
        'summary' => 'Tech summary',
        'imported_at' => now(),
    ]);

    $scienceArticle = Article::factory()->processed()->create([
        'category' => ArticleCategory::SCIENCE,
        'summary' => 'Science summary',
        'imported_at' => now(),
    ]);

    $articles = Article::where('is_processed', true)->get();

    (new SendPersonalizedNewsletterJob($newsletter, $subscriber, $articles))->handle();

    Mail::assertSent(PersonalizedNewsletterMail::class, function ($mail) use ($subscriber) {
        return $mail->hasTo($subscriber->email);
    });
});

it('skips sending when no articles match subscriber preferences', function () {
    Mail::fake();

    $newsletter = Newsletter::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'is_active' => true,
        'preferences' => [ArticleCategory::BUSINESS],
    ]);

    $article = Article::factory()->processed()->create([
        'category' => ArticleCategory::TECHNOLOGY,
        'imported_at' => now(),
    ]);

    $articles = Article::where('is_processed', true)->get();

    (new SendPersonalizedNewsletterJob($newsletter, $subscriber, $articles))->handle();

    Mail::assertNothingSent();
    expect(NewsletterSend::count())->toBe(0);
});

it('creates newsletter send record after successful delivery', function () {
    Mail::fake();

    $newsletter = Newsletter::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'is_active' => true,
        'preferences' => [ArticleCategory::TECHNOLOGY],
    ]);

    $article = Article::factory()->processed()->create([
        'category' => ArticleCategory::TECHNOLOGY,
        'summary' => 'Summary',
        'imported_at' => now(),
    ]);

    $articles = Article::where('is_processed', true)->get();

    (new SendPersonalizedNewsletterJob($newsletter, $subscriber, $articles))->handle();

    expect(NewsletterSend::count())->toBe(1)
        ->and(NewsletterSend::first()->newsletter_id)->toBe($newsletter->id)
        ->and(NewsletterSend::first()->subscriber_id)->toBe($subscriber->id)
        ->and(NewsletterSend::first()->sent_at)->not->toBeNull();
});

it('increments newsletter recipient count after successful delivery', function () {
    Mail::fake();

    $newsletter = Newsletter::factory()->create(['recipient_count' => 0]);
    $subscriber = Subscriber::factory()->create([
        'is_active' => true,
        'preferences' => [ArticleCategory::TECHNOLOGY],
    ]);

    $article = Article::factory()->processed()->create([
        'category' => ArticleCategory::TECHNOLOGY,
        'summary' => 'Summary',
        'imported_at' => now(),
    ]);

    $articles = Article::where('is_processed', true)->get();

    (new SendPersonalizedNewsletterJob($newsletter, $subscriber, $articles))->handle();

    $newsletter->refresh();

    expect($newsletter->recipient_count)->toBe(1);
});
