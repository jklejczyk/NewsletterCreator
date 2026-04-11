<?php

namespace App\Domain\Newsletter\Commands;

final readonly class UnsubscribeCommand
{
    public function __construct(public int $subscriberId) {}
}
