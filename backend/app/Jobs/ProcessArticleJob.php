<?php

namespace App\Jobs;

use App\Domain\Article\Commands\ProcessArticleCommand;
use App\Domain\Article\Models\Article;
use App\Infrastructure\Bus\CommandBus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessArticleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(private Article $article) {}

    public function handle(CommandBus $bus): void
    {
        $bus->dispatch(new ProcessArticleCommand($this->article));
    }
}
