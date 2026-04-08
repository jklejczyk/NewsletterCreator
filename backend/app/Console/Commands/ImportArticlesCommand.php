<?php

namespace App\Console\Commands;

use App\Domain\Article\Clients\NewsApiClient;
use App\Domain\Article\Clients\RssFeedIoClient;
use App\Jobs\ImportArticlesJob;
use Illuminate\Console\Command;

class ImportArticlesCommand extends Command
{
    protected $signature = 'articles:import';
    protected $description = 'Import articles from all sources';

    public function handle(NewsApiClient $newsApiClient, RssFeedIoClient $rssFeedIoClient): void
    {
        ImportArticlesJob::dispatch([$newsApiClient, $rssFeedIoClient]);

        $this->info('All articles have been imported.');
    }
}
