<?php

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Article\Models\Article;

it('returns paginated list of processed articles', function () {
    Article::factory()->processed()->count(3)->create(['imported_at' => now()]);
    Article::factory()->create(['is_processed' => false, 'imported_at' => now()]);

    $this->getJson(route('api.v1.articles.index'))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'title', 'summary', 'url', 'category', 'source', 'published_at']],
            'links',
            'meta',
        ]);
});

it('filters articles by category', function () {
    Article::factory()->processed()->create([
        'category' => ArticleCategory::TECHNOLOGY,
        'imported_at' => now(),
    ]);

    Article::factory()->processed()->create([
        'category' => ArticleCategory::SCIENCE,
        'imported_at' => now(),
    ]);

    $this->getJson(route('api.v1.articles.index', ['category' => ArticleCategory::TECHNOLOGY->value]))
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('filters articles by date range', function () {
    Article::factory()->processed()->create([
        'published_at' => '2026-04-10',
        'imported_at' => now(),
    ]);

    Article::factory()->processed()->create([
        'published_at' => '2026-04-01',
        'imported_at' => now(),
    ]);

    $this->getJson(route('api.v1.articles.index', [
        'date_from' => '2026-04-05',
        'date_to' => '2026-04-12',
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('does not expose internal fields in response', function () {
    Article::factory()->processed()->create(['imported_at' => now()]);

    $this->getJson(route('api.v1.articles.index'))
        ->assertOk()
        ->assertJsonMissing(['is_processed'])
        ->assertJsonMissingPath('data.0.content')
        ->assertJsonMissingPath('data.0.imported_at')
        ->assertJsonMissingPath('data.0.created_at');
});

it('shows single article by id', function () {
    $article = Article::factory()->processed()->create(['imported_at' => now()]);

    $this->getJson(route('api.v1.articles.show', $article))
        ->assertOk()
        ->assertJsonFragment([
            'id' => $article->id,
            'title' => $article->title,
        ])
        ->assertJsonStructure(['data' => ['id', 'title', 'summary', 'url', 'category', 'source', 'published_at']]);
});

it('returns 404 when article does not exist', function () {
    $this->getJson(route('api.v1.articles.show', ['article' => 999999]))
        ->assertNotFound();
});
