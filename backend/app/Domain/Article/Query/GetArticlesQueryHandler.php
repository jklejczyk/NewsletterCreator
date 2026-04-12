<?php

namespace App\Domain\Article\Query;

use App\Domain\Article\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;

class GetArticlesQueryHandler
{
    /** @return LengthAwarePaginator<int, Article> */
    public function handle(GetArticlesQuery $query): LengthAwarePaginator
    {
        return Article::where('is_processed', true)
            ->when($query->category, function ($q, $category) {
                return $q->where('category', $category);
            })
            ->when($query->dateFrom, function ($q, $date) {
                return $q->where('published_at', '>=', $date);
            })
            ->when($query->dateTo, function ($q, $date) {
                return $q->where('published_at', '<=', $date);
            })->latest()->paginate($query->perPage);
    }
}
