<?php

namespace App\Domain\Newsletter\Exceptions;
use RuntimeException;

final class AlreadyConfirmedException extends RuntimeException
{

    public static function create(): self
    {
        return new self('Subscriber already confirmed.');
    }
}
