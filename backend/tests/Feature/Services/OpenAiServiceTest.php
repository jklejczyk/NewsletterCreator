<?php

use App\Domain\Article\Enums\ArticleCategory;
use App\Services\OpenAiService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->service = new OpenAiService('fake-key');
});

it('summarizes article content', function () {
    Http::fake([
        'https://api.openai.com/*' => Http::response([
            'choices' => [
                ['message' => ['content' => 'To jest streszczenie artykułu.']],
            ],
        ]),
    ]);

    $summary = $this->service->summarize('Long article content...');

    expect($summary)->toBe('To jest streszczenie artykułu.');
});

it('throws exception when summarize response has unexpected format', function () {
    Http::fake([
        'https://api.openai.com/*' => Http::response([
            'choices' => [
                ['message' => ['content' => null]],
            ],
        ]),
    ]);

    $this->service->summarize('Some content');
})->throws(RuntimeException::class, 'OpenAI returned unexpected response format');

it('throws exception when summarize request fails', function () {
    Http::fake([
        'https://api.openai.com/*' => Http::response([], 500),
    ]);

    $this->service->summarize('Some content');
})->throws(Illuminate\Http\Client\RequestException::class);

it('categorizes article as technology', function () {
    Http::fake([
        'https://api.openai.com/*' => Http::response([
            'choices' => [
                ['message' => ['content' => 'technology']],
            ],
        ]),
    ]);

    $category = $this->service->categorize('Article about AI...');

    expect($category)->toBe(ArticleCategory::TECHNOLOGY);
});

it('returns general category for unknown response', function () {
    Http::fake([
        'https://api.openai.com/*' => Http::response([
            'choices' => [
                ['message' => ['content' => 'unknown_category']],
            ],
        ]),
    ]);

    $category = $this->service->categorize('Some content');

    expect($category)->toBe(ArticleCategory::GENERAL);
});

it('throws exception on invalid api key', function () {
    Http::fake([
        'https://api.openai.com/*' => Http::response([
            'error' => [
                'message' => 'Incorrect API key provided.',
                'type' => 'invalid_request_error',
            ],
        ], 401),
    ]);

    $this->service->summarize('Some content');
})->throws(Illuminate\Http\Client\RequestException::class);
