<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class UnableToPerformRequestException extends OxxaException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf('Unable to perform request, error: `%s`', $message),
            code: self::UNABLE_TO_PERFORM_REQUEST,
            previous: $previous,
        );
    }
}
