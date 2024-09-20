<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class InvalidNameserverGroupException extends OxxaException
{
    public function __construct(string $statusCode, string $statusMessage, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('`%s`: The given nsgroup cannot be found or deleted', $statusCode),
            code: self::INVALID_NAMESERVER_GROUP,
            statusCode: $statusCode,
            statusMessage: $statusMessage,
            previous: $previous,
        );
    }
}
