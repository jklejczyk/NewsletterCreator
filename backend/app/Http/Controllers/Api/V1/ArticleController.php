<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Article\Models\Article;
use App\Domain\Article\Query\GetArticlesQuery;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Infrastructure\Bus\QueryBus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    public function index(Request $request, QueryBus $bus): AnonymousResourceCollection
    {
        $result = $bus->ask(new GetArticlesQuery(
            category: $request->enum('category', ArticleCategory::class),
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
            perPage: $request->integer('per_page', 20),
        ));

        return ArticleResource::collection($result);
    }

    public function show(Article $article): ArticleResource
    {
        return new ArticleResource($article);
    }
}
