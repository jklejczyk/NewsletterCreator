<?php

namespace App\Services;

use App\Domain\Article\Enums\ArticleCategory;
use App\Interfaces\AiClientInterface;
use Illuminate\Support\Facades\Http;

class OpenAiService implements AiClientInterface
{
    public function __construct(private string $apiKey,
        private string $model = 'gpt-4o-mini',
    ) {}

    public function summarize(string $content): string
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant. Summarize the following article in 2-3 sentences in Polish.',
                ],
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
        ])->throw();

        $content = $response->json('choices.0.message.content');

        if (! is_string($content)) {
            throw new \RuntimeException('OpenAI returned unexpected response format');
        }

        return $content;
    }

    public function categorize(string $content): ArticleCategory
    {
        $categories = implode(', ', array_map(fn ($case) => $case->value, ArticleCategory::cases()));

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "Categorize the following article into exactly one of these categories: {$categories}. Respond with only the category name, nothing else.",
                ],
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
        ])->throw();

        $result = $response->json('choices.0.message.content');
        $category = ArticleCategory::tryFrom(trim($result));

        if ($category === null) {
            return ArticleCategory::GENERAL;
        }

        return $category;
    }
}
