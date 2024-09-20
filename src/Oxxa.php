<?php

namespace Cyberfusion\Oxxa;

use Cyberfusion\Oxxa\Contracts\OxxaClient;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Exceptions\DomainNotInAdministrationException;
use Cyberfusion\Oxxa\Exceptions\DomainNotMutatableException;
use Cyberfusion\Oxxa\Exceptions\InsufficientFundsException;
use Cyberfusion\Oxxa\Exceptions\InvalidAdminIdentityException;
use Cyberfusion\Oxxa\Exceptions\InvalidBillingIdentityException;
use Cyberfusion\Oxxa\Exceptions\InvalidCredentialsException;
use Cyberfusion\Oxxa\Exceptions\InvalidNameserverGroupException;
use Cyberfusion\Oxxa\Exceptions\InvalidRegistrantIdentityException;
use Cyberfusion\Oxxa\Exceptions\InvalidTechIdentityException;
use Cyberfusion\Oxxa\Exceptions\MissingPasswordException;
use Cyberfusion\Oxxa\Exceptions\MissingUsernameException;
use Cyberfusion\Oxxa\Exceptions\UnableToPerformRequestException;
use Cyberfusion\Oxxa\Traits\ResponseParser;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class Oxxa implements OxxaClient
{
    use ResponseParser;

    private const TIMEOUT = 180;

    private const VERSION = '2.11.0';

    private const USER_AGENT = 'oxxa-api-client/'.self::VERSION;

    private string $baseUri = 'https://api.oxxa.com/command.php';

    /**
     * @throws MissingUsernameException
     * @throws MissingPasswordException
     */
    public function __construct(
        private readonly string $username,
        private readonly string $password,
        private bool $testMode = false,
        private readonly Factory|PendingRequest|null $client = null,
    ) {
        if (Str::length($this->username) === 0) {
            throw new MissingUsernameException();
        }
        if (Str::length($this->password) === 0) {
            throw new MissingPasswordException();
        }
    }

    public function setBaseUri(string $baseUri): self
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    public function enabledTestMode(): self
    {
        $this->testMode = true;

        return $this;
    }

    public function disableTestMode(): self
    {
        $this->testMode = false;

        return $this;
    }

    /**
     * Perform the request.
     *
     * @throws InvalidCredentialsException
     * @throws DomainNotInAdministrationException
     * @throws InsufficientFundsException
     * @throws InvalidAdminIdentityException
     * @throws InvalidTechIdentityException
     * @throws InvalidBillingIdentityException
     * @throws InvalidRegistrantIdentityException
     * @throws InvalidNameserverGroupException
     * @throws DomainNotMutatableException
     * @throws UnableToPerformRequestException
     */
    public function request(array $parameters = []): Crawler
    {
        // Create a new pending request with the user agent and timeout when no client was injected
        $pendingRequest = $this->client ?? (new Factory())
            ->withUserAgent(self::USER_AGENT)
            ->timeout(self::TIMEOUT);

        // Always add the API credentials to the request
        $parameters['apiuser'] = $this->username;
        $parameters['apipassword'] = $this->password;

        // Add the test parameter if the test mode is enabled
        if ($this->testMode) {
            $parameters['test'] = 'Y';
        }

        // Perform the request and throw an exception when the request failed
        try {
            $response = $pendingRequest
                ->baseUrl($this->baseUri)
                ->get('', $parameters)
                ->throw();
        } catch (Throwable $throwable) {
            throw new UnableToPerformRequestException($throwable->getMessage());
        }

        // Parse the response body as XML
        $xml = new Crawler($response->body());

        // Check the status code in the response for common errors
        $this->checkStatus($xml);

        return $xml;
    }

    /**
     * Checks the status code in the response and throws an exception if the status code represents a common error.
     *
     * @throws InvalidCredentialsException
     * @throws DomainNotInAdministrationException
     * @throws InsufficientFundsException
     * @throws InvalidAdminIdentityException
     * @throws InvalidTechIdentityException
     * @throws InvalidBillingIdentityException
     * @throws InvalidRegistrantIdentityException
     * @throws InvalidNameserverGroupException
     * @throws DomainNotMutatableException
     */
    private function checkStatus(Crawler $crawler): void
    {
        $statusCode = $this->getStatusCode($crawler);
        $statusMessage = $this->getStatusDescription($crawler);

        switch ($statusCode) {
            case StatusCode::STATUS_INVALID_CREDENTIALS:
                throw new InvalidCredentialsException(statusCode: $statusCode, statusMessage: $statusMessage);
            case StatusCode::STATUS_DOMAIN_NOT_IN_ADMINISTRATION:
                throw new DomainNotInAdministrationException(statusCode: $statusCode, statusMessage: $statusMessage);
            case StatusCode::STATUS_INSUFFICIENT_FUNDS:
                throw new InsufficientFundsException(statusCode: $statusCode, statusMessage: $statusMessage);
            case StatusCode::STATUS_INVALID_ADMIN_IDENTITY:
                throw new InvalidAdminIdentityException(statusCode: $statusCode, statusMessage: $statusMessage);
            case StatusCode::STATUS_INVALID_TECH_IDENTITY:
                throw new InvalidTechIdentityException(statusCode: $statusCode, statusMessage: $statusMessage);
            case StatusCode::STATUS_INVALID_BILLING_IDENTITY:
                throw new InvalidBillingIdentityException(statusCode: $statusCode, statusMessage: $statusMessage);
            case StatusCode::STATUS_INVALID_REGISTRANT_IDENTITY:
                throw new InvalidRegistrantIdentityException(statusCode: $statusCode, statusMessage: $statusMessage);
            case StatusCode::STATUS_INVALID_NAME_SERVER_GROUP:
                throw new InvalidNameserverGroupException(statusCode: $statusCode, statusMessage: $statusMessage);
            case StatusCode::STATUS_DOMAIN_NOT_MUTATABLE:
                throw new DomainNotMutatableException($statusCode, $statusMessage);
        }
    }

    public function dnssec(): Endpoints\DnssecEndpoint
    {
        return new Endpoints\DnssecEndpoint($this);
    }

    public function domain(): Endpoints\DomainEndpoint
    {
        return new Endpoints\DomainEndpoint($this);
    }

    public function glue(): Endpoints\GlueEndpoint
    {
        return new Endpoints\GlueEndpoint($this);
    }

    public function identity(): Endpoints\IdentityEndpoint
    {
        return new Endpoints\IdentityEndpoint($this);
    }

    public function nameserverGroup(): Endpoints\NameserverGroupEndpoint
    {
        return new Endpoints\NameserverGroupEndpoint($this);
    }

    public function task(): Endpoints\TaskEndpoint
    {
        return new Endpoints\TaskEndpoint($this);
    }

    public function tld(): Endpoints\UserTldEndpoint
    {
        return new Endpoints\UserTldEndpoint($this);
    }

    public function products(): Endpoints\ProductEndpoint
    {
        return new Endpoints\ProductEndpoint($this);
    }
}
