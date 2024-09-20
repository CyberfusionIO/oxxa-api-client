<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class InvalidBillingIdentityException extends OxxaException
{
    public function __construct(string $statusCode, string $statusMessage, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('`%s`: Your chosen billing identity doesn\'t exist', $statusCode),
            code: self::INVALID_BILLING_IDENTITY,
            statusCode: $statusCode,
            statusMessage: $statusMessage,
            previous: $previous,
        );
    }
}
