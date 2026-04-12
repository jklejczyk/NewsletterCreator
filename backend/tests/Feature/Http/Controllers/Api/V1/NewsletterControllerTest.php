<?php

use App\Domain\Newsletter\Models\Newsletter;
use App\Domain\Newsletter\Models\NewsletterSend;
use App\Domain\Newsletter\Models\Subscriber;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

it('returns paginated list of newsletters for authenticated user', function () {
    Sanctum::actingAs(User::factory()->create());

    Newsletter::factory()->count(3)->create();

    $this->getJson(route('api.v1.newsletters.index'))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'subject', 'status', 'sent_at', 'recipient_count']],
            'links',
            'meta',
        ]);
});

it('returns 401 when not authenticated on index', function () {
    $this->getJson(route('api.v1.newsletters.index'))
        ->assertUnauthorized();
});

it('shows newsletter stats with sends', function () {
    Sanctum::actingAs(User::factory()->create());

    $newsletter = Newsletter::factory()->create();
    $subscriber = Subscriber::factory()->create();

    NewsletterSend::factory()->create([
        'newsletter_id' => $newsletter->id,
        'subscriber_id' => $subscriber->id,
    ]);

    $this->getJson(route('api.v1.newsletters.stats', $newsletter))
        ->assertOk()
        ->assertJsonPath('data.id', $newsletter->id)
        ->assertJsonCount(1, 'data.sends');
});

it('returns 401 when not authenticated on stats', function () {
    $newsletter = Newsletter::factory()->create();

    $this->getJson(route('api.v1.newsletters.stats', $newsletter))
        ->assertUnauthorized();
});

it('triggers newsletter send for authenticated user', function () {
    Event::fake();
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.v1.newsletters.send'))
        ->assertStatus(202);
});

it('returns 401 when not authenticated on send', function () {
    $this->postJson(route('api.v1.newsletters.send'))
        ->assertUnauthorized();
});

it('returns 404 for non-existent newsletter stats', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('api.v1.newsletters.stats', ['newsletter' => 999999]))
        ->assertNotFound();
});
