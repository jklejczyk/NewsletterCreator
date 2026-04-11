<?php

namespace App\Domain\Newsletter\Exceptions;
use RuntimeException;
final class ConfirmationTokenExpiredException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Confirmation token expired.');
    }
}
