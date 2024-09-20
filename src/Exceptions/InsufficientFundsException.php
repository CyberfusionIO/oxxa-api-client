<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class InsufficientFundsException extends OxxaException
{
    public function __construct(string $statusCode, string $statusMessage, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('`%s`: You have insufficient funds in your account to execute this request.', $statusCode),
            code: self::INSUFFICIENT_FUNDS,
            statusCode: $statusCode,
            statusMessage: $statusMessage,
            previous: $previous,
        );
    }
}
