<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class InvalidRegistrantIdentityException extends OxxaException
{
    public function __construct(string $statusCode, string $statusMessage, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('`%s`: Your chosen registrant identity doesn\'t exist', $statusCode),
            code: self::INVALID_REGISTRANT_IDENTITY,
            statusCode: $statusCode,
            statusMessage: $statusMessage,
            previous: $previous,
        );
    }
}
