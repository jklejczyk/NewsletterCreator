<?php

namespace App\Console\Commands;

use App\Domain\Newsletter\Commands\CreateNewsletterCommand;
use App\Infrastructure\Bus\CommandBus;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('newsletter:send')]
#[Description('Send newsletter to subscribers')]
class SendNewsletterCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(CommandBus $bus): void
    {
        $bus->dispatch(new CreateNewsletterCommand);
        $this->info('Newsletter dispatch initiated.');
    }
}
