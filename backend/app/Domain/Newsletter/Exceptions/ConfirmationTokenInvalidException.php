<?php

namespace App\Domain\Newsletter\Exceptions;

use RuntimeException;

final class ConfirmationTokenInvalidException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Confirmation token is invalid or unknown.');
    }
}
