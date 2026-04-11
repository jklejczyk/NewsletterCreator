<?php

namespace App\Jobs;

use App\Domain\Article\Clients\NewsApiClient;
use App\Domain\Article\Clients\RssFeedIoClient;
use App\Domain\Article\Commands\ImportArticlesCommand;
use App\Infrastructure\Bus\CommandBus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportArticlesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function handle(CommandBus $bus, NewsApiClient $newsApi, RssFeedIoClient $rss): void
    {
        $bus->dispatch(new ImportArticlesCommand([$newsApi, $rss]));
    }
}
