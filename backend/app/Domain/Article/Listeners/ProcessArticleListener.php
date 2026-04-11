<?php

namespace App\Domain\Article\Listeners;

use App\Domain\Article\Events\ArticleImported;
use App\Jobs\ProcessArticleJob;

class ProcessArticleListener
{
    public function handle(ArticleImported $event): void
    {
        ProcessArticleJob::dispatch($event->article);
    }
}
