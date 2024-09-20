<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class InvalidCredentialsException extends OxxaException
{
    public function __construct(string $statusCode, string $statusMessage, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('`%s`: Username/password combination invalid or account has been disabled.', $statusCode),
            code: self::INVALID_CREDENTIALS,
            statusCode: $statusCode,
            statusMessage: $statusMessage,
            previous: $previous,
        );
    }
}
