<?php

namespace App\Domain\Newsletter\Commands;

final readonly class ConfirmSubscriptionCommand
{
    public function __construct(public string $token) {}
}
