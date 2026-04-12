<?php

namespace App\Domain\Article\Commands;

use App\Domain\Article\Events\ArticleImported;
use App\Domain\Article\Models\Article;

class ImportArticlesCommandHandler
{
    public function handle(ImportArticlesCommand $command): void
    {
        foreach ($command->sources as $source) {
            $articles = $source->fetch();

            foreach ($articles as $articleData) {
                $articleData['imported_at'] = now();
                $article = Article::firstOrCreate(['url' => $articleData['url']], $articleData);

                if ($article->wasRecentlyCreated) {
                    event(new ArticleImported($article));
                }
            }
        }
    }
}
