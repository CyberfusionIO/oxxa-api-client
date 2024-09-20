<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class DomainNotMutatableException extends OxxaException
{
    public function __construct(string $statusCode, string $statusMessage, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('`%s`: Given domain isn\'t mutatable.', $statusCode),
            code: self::DOMAIN_TAKEN,
            statusCode: $statusCode,
            statusMessage: $statusMessage,
            previous: $previous,
        );
    }
}
