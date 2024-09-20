<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class MissingUsernameException extends OxxaException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(
            message: 'Missing the username in the client',
            code: self::MISSING_USERNAME,
            previous: $previous
        );
    }
}
