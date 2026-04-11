<?php

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Newsletter\Events\SubscriberRegistered;
use App\Domain\Newsletter\Models\Subscriber;
use App\Jobs\SendSubscriptionConfirmationJob;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

it('registers new subscriber and returns 201', function () {
    Event::fake();

    $this->postJson(route('api.v1.subscribers.store'), [
        'email' => 'test@example.com',
        'name' => 'Jan Kowalski',
        'preferences' => [ArticleCategory::TECHNOLOGY->value, ArticleCategory::SCIENCE->value],
    ])->assertCreated();

    expect(Subscriber::where('email', 'test@example.com')->exists())->toBeTrue();

    Event::assertDispatched(SubscriberRegistered::class);
});

it('dispatches send confirmation job through the event chain', function () {
    Queue::fake();

    $this->postJson(route('api.v1.subscribers.store'), [
        'email' => 'test@example.com',
        'name' => 'Jan Kowalski',
        'preferences' => [ArticleCategory::TECHNOLOGY->value],
    ])->assertCreated();

    Queue::assertPushed(SendSubscriptionConfirmationJob::class);
});

it('requires email', function () {
    $this->postJson(route('api.v1.subscribers.store'), [
        'name' => 'Jan Kowalski',
        'preferences' => [ArticleCategory::TECHNOLOGY->value],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires valid email format', function () {
    $this->postJson(route('api.v1.subscribers.store'), [
        'email' => 'not-an-email',
        'name' => 'Jan Kowalski',
        'preferences' => [ArticleCategory::TECHNOLOGY->value],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects duplicate email', function () {
    Subscriber::factory()->create(['email' => 'test@example.com']);

    $this->postJson(route('api.v1.subscribers.store'), [
        'email' => 'test@example.com',
        'name' => 'Jan Kowalski',
        'preferences' => [ArticleCategory::TECHNOLOGY->value],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires name', function () {
    $this->postJson(route('api.v1.subscribers.store'), [
        'email' => 'test@example.com',
        'preferences' => [ArticleCategory::TECHNOLOGY->value],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('requires at least one preference', function () {
    $this->postJson(route('api.v1.subscribers.store'), [
        'email' => 'test@example.com',
        'name' => 'Jan Kowalski',
        'preferences' => [],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['preferences']);
});

it('rejects unknown preference category', function () {
    $this->postJson(route('api.v1.subscribers.store'), [
        'email' => 'test@example.com',
        'name' => 'Jan Kowalski',
        'preferences' => ['nonexistent-category'],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['preferences.0']);
});

it('confirms subscriber with valid token', function () {
    $subscriber = Subscriber::factory()->unconfirmed()->inactive()->create([
        'confirmation_token' => 'valid-token',
        'confirmation_sent_at' => now(),
    ]);

    $this->getJson(route('api.v1.subscribers.confirm', ['token' => 'valid-token']))
        ->assertOk();

    $subscriber->refresh();

    expect($subscriber->is_active)->toBeTrue()
        ->and($subscriber->confirmed_at)->not->toBeNull();
});

it('returns 404 when confirmation token does not exist', function () {
    $this->getJson(route('api.v1.subscribers.confirm', ['token' => 'nonexistent-token']))
        ->assertNotFound();
});

it('returns 410 when confirmation token has expired', function () {
    $ttlHours = config('newsletter.confirmation_ttl_hours');

    Subscriber::factory()->unconfirmed()->inactive()->create([
        'confirmation_token' => 'expired-token',
        'confirmation_sent_at' => now()->subHours($ttlHours + 1),
    ]);

    $this->getJson(route('api.v1.subscribers.confirm', ['token' => 'expired-token']))
        ->assertStatus(410);
});
