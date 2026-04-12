<?php

namespace App\Http\Resources;

use App\Domain\Article\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'url' => $this->url,
            'category' => $this->category,
            'source' => $this->source,
            'published_at' => $this->published_at?->format('Y-m-d'),
        ];
    }
}
