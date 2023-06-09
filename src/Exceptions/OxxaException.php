<?php

namespace Cyberfusion\Oxxa\Exceptions;

use Exception;

class OxxaException extends Exception
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

    public static function missingUsername(): OxxaException
    {
        return new self(
            'Missing the username in the client',
            self::MISSING_USERNAME
        );
    }

    public static function missingPassword(): OxxaException
    {
        return new self(
            'Missing the password in the client',
            self::MISSING_PASSWORD
        );
    }

    public static function restrictedDomain(string $statusCode): OxxaException
    {
        return new self(
            sprintf('`%s`: This domain is not under your administration.', $statusCode),
            self::RESTRICTED_DOMAIN
        );
    }

    public static function invalidTechIdentity(string $statusCode): OxxaException
    {
        return new self(
            sprintf('`%s`: Your chosen tech identity doesn\'t exist', $statusCode),
            self::INVALID_TECH_IDENTITY
        );
    }

    public static function invalidRegistrantIdentity(string $statusCode): OxxaException
    {
        return new self(
            sprintf('`%s`: Your chosen registrant identity doesn\'t exist', $statusCode),
            self::INVALID_REGISTRANT_IDENTITY
        );
    }

    public static function invalidBillingIdentity(string $statusCode): OxxaException
    {
        return new self(
            sprintf('`%s`: Your chosen billing identity doesn\'t exist', $statusCode),
            self::INVALID_BILLING_IDENTITY
        );
    }

    public static function invalidAdminIdentity(string $statusCode): OxxaException
    {
        return new self(
            sprintf('`%s`: Your chosen admin identity doesn\'t exist', $statusCode),
            self::INVALID_ADMIN_IDENTITY
        );
    }

    public static function invalidCredentials(string $statusCode): OxxaException
    {
        return new self(
            sprintf('`%s`: Username/password combination invalid or account has been disabled.', $statusCode),
            self::INVALID_CREDENTIALS
        );
    }

    public static function domainTaken(string $statusCode): OxxaException
    {
        return new self(
            sprintf('`%s`: Given domain is already taken.', $statusCode),
            self::DOMAIN_TAKEN
        );
    }

    public static function insufficientFunds(string $statusCode): OxxaException
    {
        return new self(
            sprintf('`%s`: You have insufficient funds in your account to execute this request.', $statusCode),
            self::INSUFFICIENT_FUNDS
        );
    }

    public static function invalidNameServerGroup(string $statusCode): OxxaException
    {
        return new self(
            sprintf('`%s`: The given nsgroup cannot be found or deleted', $statusCode),
            self::INVALID_NAMESERVER_GROUP
        );
    }

    public static function unableToPerformRequest(string $message): OxxaException
    {
        return new self(
            sprintf('Unable to perform request, error: `%s`', $message),
            self::UNABLE_TO_PERFORM_REQUEST
        );
    }
}
