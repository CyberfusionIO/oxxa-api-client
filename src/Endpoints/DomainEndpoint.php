<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Enum\Toggle;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\Domain;
use Cyberfusion\Oxxa\Requests\DomainListRequest;
use Cyberfusion\Oxxa\Support\OxxaResult;
use DateTime;
use Symfony\Component\DomCrawler\Crawler;

class DomainEndpoint extends Endpoint implements EndpointContract
{
    /**
     * Determines if the domain is available.
     *
     * @throws OxxaException
     */
    public function check(string $sld, string $tld): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'domain_check',
                'tld' => $tld,
                'sld' => $sld,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'available' => $statusCode === StatusCode::STATUS_DOMAIN_AVAILABLE,
            ],
            status: $statusCode,
        );
    }

    /**
     * Returns all the available domains.
     *
     * @throws OxxaException
     */
    public function list(DomainListRequest $request = null): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'domain_list'],
                $request?->toArray() ?? []
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_DOMAINS_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        $domains = [];
        $xml
            ->filter('channel > order > details > domain')
            ->each(function (Crawler $domainNode) use (&$domains) {
                $domains[] = new Domain(
                    identityAdmin: $domainNode->filter('identity-admin')->text(),
                    identityTech: $domainNode->filter('identity-tech')->text(),
                    identityBilling: $domainNode->filter('identity-billing')->text(),
                    identityRegistrant: $domainNode->filter('identity-registrant')->text(),
                    nameserverGroup: $domainNode->filter('nsgroup')->text(),
                    domain: $domainNode->filter('domainname')->text(),
                    expireDate: DateTime::createFromFormat('Y-m-d', $domainNode->filter('expire_date')->text())->setTime(0, 0),
                    lock: $domainNode->filter('lock')->count()
                        ? Toggle::toBoolean($domainNode->filter('lock')->text())
                        : null,
                );
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'domains' => $domains,
            ],
            status: $statusCode,
        );
    }

    /**
     * Returns information about the domain.
     *
     * @throws OxxaException
     */
    public function get(string $sld, string $tld): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'domain_inf',
                'sld' => $sld,
                'tld' => $tld,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_DOMAIN_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        $detailsNode = $xml->filter('channel > order > details');

        $domain = new Domain(
            identityAdmin: $detailsNode->filter('identity-admin')->text(),
            identityTech: $detailsNode->filter('identity-tech')->text(),
            identityBilling: $detailsNode->filter('identity-billing')->text(),
            identityRegistrant: $detailsNode->filter('identity-registrant')->text(),
            identityReseller: $detailsNode->filter('identity-reseller')->count()
                ? $detailsNode->filter('identity-reseller')->text()
                : null,
            nameserverGroup: $detailsNode->filter('nsgroup')->text(),
            autoRenew: Toggle::toBoolean($detailsNode->filter('autorenew')->text()),
            expireDate: DateTime::createFromFormat('d-m-Y', $detailsNode->filter('expire_date')->text())->setTime(0, 0),
            useTrustee: $detailsNode->filter('usetrustee')->count()
                ? Toggle::toBoolean($detailsNode->filter('usetrustee')->text())
                : null,
            dnsSec: Toggle::toBoolean($detailsNode->filter('dnssec')->text()),
        );

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'domain' => $domain,
            ],
            status: $statusCode,
        );
    }

    /**
     * Register the domain.
     *
     * @throws OxxaException
     */
    public function register(Domain $domain): OxxaResult
    {
        $requiredFields = [
            'identity-admin',
            'identity-registrant',
            'nsgroup',
            'sld',
            'tld',
            'period',
            'autorenew',
        ];

        if ($domain->missingAny($requiredFields)) {
            return new OxxaResult(
                success: false,
                message: sprintf(
                    'The domain is missing the required fields: `%s`',
                    implode(', ', $domain->missingFields($requiredFields))
                )
            );
        }

        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'register'],
                $domain->toArray()
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_DOMAIN_REGISTER_REQUESTED,
            message: $statusDescription,
            status: $statusCode,
        );
    }

    /**
     * Determines the domain registration status.
     *
     * @throws OxxaException
     */
    public function registerStatus(Domain $domain): OxxaResult
    {
        if (empty($domain->tld) || empty($domain->sld)) {
            return new OxxaResult(
                success: false,
                message: 'The TLD and SLD must be provided'
            );
        }

        $xml = $this
            ->client
            ->request([
                'command' => 'register_status',
                'sld' => $domain->sld,
                'tld' => $domain->tld,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'done' => $xml->filter('channel > order > done')->text() === 'TRUE',
                'description' => $statusDescription,
            ],
            status: $statusCode,
        );
    }

    /**
     * Send the EPP token.
     *
     * @throws OxxaException
     */
    public function sendEpp(string $sld, string $tld): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'domain_epp',
                'sld' => $sld,
                'tld' => $tld,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_DOMAIN_TOKEN_SENT,
            message: $statusDescription,
            data: [
                'epp' => $xml->filter('channel > order > details')->text(),
            ],
            status: $statusCode,
        );
    }

    /**
     * Update the domain. Be aware, when changing the identity-registrant some TLD's handle this as a transfer to a new
     * account. The TLD might charge you for that.
     *
     * @throws OxxaException
     */
    public function update(Domain $domain): OxxaResult
    {
        if (empty($domain->tld) || empty($domain->sld)) {
            return new OxxaResult(
                success: false,
                message: 'The TLD and SLD must be provided'
            );
        }

        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'domain_upd'],
                $domain->toArray()
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_DOMAIN_UPDATED,
            message: $statusDescription,
            status: $statusCode,
        );
    }

    /**
     * Update the auto renewal for the domain.
     *
     * @throws OxxaException
     */
    private function updateAutoRenewal(string $sld, string $tld, bool $autoRenew = false): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'autorenew',
                'autorenew' => Toggle::fromBoolean($autoRenew),
                'sld' => $sld,
                'tld' => $tld,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_DOMAIN_AUTORENEW_CHANGED,
            message: $statusDescription,
            status: $statusCode,
        );
    }

    /**
     * Enables the auto-renewal for the domain.
     *
     * @throws OxxaException
     */
    public function enableAutoRenewal(string $sld, string $tld): OxxaResult
    {
        return $this->updateAutoRenewal(
            sld: $sld,
            tld: $tld,
            autoRenew: true
        );
    }

    /**
     * Disables the auto-renewal for the domain.
     *
     * @throws OxxaException
     */
    public function disableAutoRenewal(string $sld, string $tld): OxxaResult
    {
        return $this->updateAutoRenewal(
            sld: $sld,
            tld: $tld,
            autoRenew: false
        );
    }

    /**
     * Update the lock for the domain.
     *
     * @throws OxxaException
     */
    private function updateLock(string $sld, string $tld, bool $lock = false): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'lock',
                'sld' => $sld,
                'tld' => $tld,
                'lock' => Toggle::fromBoolean($lock),
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_DOMAIN_LOCK_CHANGED,
            message: $statusDescription,
            status: $statusCode,
        );
    }

    /**
     * Enables the lock for the domain.
     *
     * @throws OxxaException
     */
    public function enableLock(string $sld, string $tld): OxxaResult
    {
        return $this->updateLock(
            sld: $sld,
            tld: $tld,
            lock: true
        );
    }

    /**
     * Disables the lock for the domain.
     *
     * @throws OxxaException
     */
    public function disableLock(string $sld, string $tld): OxxaResult
    {
        return $this->updateLock(
            sld: $sld,
            tld: $tld,
            lock: false
        );
    }

    /**
     * Update the nameservers of the domain.
     *
     * @throws OxxaException
     */
    public function updateNameservers(Domain $domain, bool $dnssecDelete = null): OxxaResult
    {
        if (is_null($domain->tld) || is_null($domain->sld)) {
            return new OxxaResult(
                success: false,
                message: 'The TLD and SLD must be provided'
            );
        }

        $parameters = [
            'command' => 'domain_ns_upd',
            'sld' => $domain->sld,
            'tld' => $domain->tld,
        ];
        if (! is_null($dnssecDelete)) {
            $parameters['dnssec_delete'] = $dnssecDelete ? 'Y' : 'N';
        }
        if (! is_null($domain->nameserverGroup)) {
            $parameters['nsgroup'] = $domain->nameserverGroup;
        }
        if (! is_null($domain->nameserverGroup)) {
            $parameters['handle'] = $domain->dnsTemplate;
        }

        $xml = $this
            ->client
            ->request($parameters);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_DOMAIN_UPDATED,
            message: $statusDescription,
            status: $statusCode,
        );
    }

    /**
     * Transfer the domain to the current account.
     *
     * @throws OxxaException
     */
    public function transfer(Domain $domain): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'transfer'],
                $domain->toArray()
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_DOMAIN_TRANSFER_REQUESTED,
            message: $statusDescription,
            status: $statusCode,
        );
    }

    /**
     * Determines the domain transfer status.
     *
     * @throws OxxaException
     */
    public function transferStatus(Domain $domain): OxxaResult
    {
        if (empty($domain->tld) || empty($domain->sld)) {
            return new OxxaResult(
                success: false,
                message: 'The TLD and SLD must be provided'
            );
        }

        $xml = $this
            ->client
            ->request([
                'command' => 'transfer_status',
                'sld' => $domain->sld,
                'tld' => $domain->tld,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'done' => $xml->filter('channel > order > done')->text() === 'TRUE',
                'description' => $statusDescription,
                'code' => $statusCode,
            ],
            status: $$statusCode,
        );
    }

    /**
     * Restores the domain from quarantine. Restoring a domain from quarantine costs money!
     *
     * @throws OxxaException
     */
    public function fromQuarantine(string $sld, string $tld): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'domain_restore',
                'sld' => $sld,
                'tld' => $tld,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_DOMAIN_RESTORED,
            message: $statusDescription,
            status: $statusCode,
        );
    }

    /**
     * Puts the domain in quarantine. Be careful, it costs money to restore the domain name, so be sure you know what
     * you're doing.
     *
     * @throws OxxaException
     */
    public function toQuarantine(string $sld, string $tld): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'domain_del',
                'sld' => $sld,
                'tld' => $tld,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_DOMAIN_DELETED,
            message: $statusDescription,
            status: $statusCode,
        );
    }
}
