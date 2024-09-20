<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class DomainNotInAdministrationException extends OxxaException
{
    public function __construct(string $statusCode, string $statusMessage, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('`%s`: This domain is not under your administration.', $statusCode),
            code: self::RESTRICTED_DOMAIN,
            statusCode: $statusCode,
            statusMessage: $statusMessage,
            previous: $previous,
        );
    }
}
