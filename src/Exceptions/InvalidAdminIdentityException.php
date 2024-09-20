<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class InvalidAdminIdentityException extends OxxaException
{
    public function __construct(string $statusCode, string $statusMessage, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('`%s`: Your chosen admin identity doesn\'t exist', $statusCode),
            code: self::INVALID_ADMIN_IDENTITY,
            statusCode: $statusCode,
            statusMessage: $statusMessage,
            previous: $previous,
        );
    }
}
