<?php

namespace App\Console\Commands;

use App\Jobs\ImportArticlesJob;
use Illuminate\Console\Command;

class ImportArticlesCommand extends Command
{
    protected $signature = 'articles:import';

    protected $description = 'Import articles from all sources';

    public function handle(): void
    {
        ImportArticlesJob::dispatch();

        $this->info('Import job dispatched.');
    }
}
