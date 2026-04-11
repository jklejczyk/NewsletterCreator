<?php

use App\Domain\Newsletter\Commands\UnsubscribeCommand;
use App\Domain\Newsletter\Commands\UnsubscribeCommandHandler;
use App\Domain\Newsletter\Models\Subscriber;
use Illuminate\Database\Eloquent\ModelNotFoundException;

it('deletes subscriber from database', function () {
    $subscriber = Subscriber::factory()->create();

    $handler = new UnsubscribeCommandHandler;
    $handler->handle(new UnsubscribeCommand($subscriber->id));

    expect(Subscriber::find($subscriber->id))->toBeNull();
});

it('throws model not found exception when subscriber does not exist', function () {
    $handler = new UnsubscribeCommandHandler;
    $handler->handle(new UnsubscribeCommand(999999));
})->throws(ModelNotFoundException::class);
