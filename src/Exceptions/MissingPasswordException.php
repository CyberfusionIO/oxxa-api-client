<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Throwable;

class MissingPasswordException extends OxxaException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(
            message: 'Missing the password in the client',
            code: self::MISSING_PASSWORD,
            previous: $previous,
        );
    }
}
