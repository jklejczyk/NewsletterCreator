<?php

use App\Jobs\ImportArticlesJob;
use Illuminate\Support\Facades\Queue;

it('dispatches import articles job', function () {
    Queue::fake();

    $this->artisan('articles:import')
        ->assertSuccessful();

    Queue::assertPushed(ImportArticlesJob::class);
});
