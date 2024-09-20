<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Exception;
use Throwable;

abstract class OxxaException extends Exception
{
    public const MISSING_USERNAME = 100;

    public const MISSING_PASSWORD = 101;

    public const RESTRICTED_DOMAIN = 200;

    public const INVALID_TECH_IDENTITY = 201;

    public const INVALID_REGISTRANT_IDENTITY = 202;

    public const INVALID_BILLING_IDENTITY = 203;

    public const INVALID_ADMIN_IDENTITY = 204;

    public const INVALID_CREDENTIALS = 300;

    public const DOMAIN_TAKEN = 400;

    public const INSUFFICIENT_FUNDS = 401;

    public const INVALID_NAMESERVER_GROUP = 402;

    public const UNABLE_TO_PERFORM_REQUEST = 500;

    public function __construct(
        string $message,
        int $code,
        public readonly ?string $statusCode = null,
        public readonly ?string $statusMessage = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
