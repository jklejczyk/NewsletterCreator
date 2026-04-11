<?php

namespace App\Domain\Newsletter\Commands;

use App\Domain\Newsletter\Models\Subscriber;

/**
 * Celowe uproszczenie: wypisanie kasuje rekord subskrybenta twardo z bazy.
 * W produkcji należałoby, zamiast tego ustawić is_active = false (soft delete),
 * żeby zachować historię wysyłek i umożliwić ponowną aktywację subskrybenta.
 */
class UnsubscribeCommandHandler
{
    public function handle(UnsubscribeCommand $command): void
    {
        $subscriber = Subscriber::findOrFail($command->subscriberId);

        $subscriber->delete();
    }
}
