<?php

namespace App\Jobs;

use App\Domain\Article\Commands\ImportArticlesCommand;
use App\Domain\Article\Interfaces\ArticleSourceInterface;
use App\Infrastructure\Bus\CommandBus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportArticlesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    /**
     * @param array<int, ArticleSourceInterface> $sources
     */
    public function __construct(private array $sources) {}

    public function handle(CommandBus $bus): void
    {
        $bus->dispatch(new ImportArticlesCommand($this->sources));
    }
}
