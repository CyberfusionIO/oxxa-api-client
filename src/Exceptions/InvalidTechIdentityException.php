<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class InvalidTechIdentityException extends OxxaException
{
    public function __construct(string $statusCode, string $statusMessage, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('`%s`: Your chosen tech identity doesn\'t exist', $statusCode),
            code: self::INVALID_TECH_IDENTITY,
            statusCode: $statusCode,
            statusMessage: $statusMessage,
            previous: $previous,
        );
    }
}
