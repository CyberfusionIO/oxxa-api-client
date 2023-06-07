<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Enum\Toggle;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\Domain as DomainModel;
use Cyberfusion\Oxxa\Requests\DomainListRequest;
use Cyberfusion\Oxxa\Support\OxxaResult;
use DOMElement;
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

        return new OxxaResult(
            success: true,
            message: $this->getStatusDescription($xml),
            data: [
                'available' => $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_AVAILABLE,
            ]
        );
    }

    /**
     * Returns all the available domains.
     *
     * @throws OxxaException
     */
    public function list(DomainListRequest $request = null): OxxaResult
    {
        $parameters = [
            'command' => 'domain_list',
        ];
        if (! is_null($request)) {
            $parameters = array_merge($parameters, $request->serialize());
        }

        $xml = $this
            ->client
            ->request($parameters);

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_DOMAINS_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription
            );
        }

        $domains = [];
        $xml
            ->filter('channel > order > details > domain')
            ->each(function (Crawler $domainNode) use (&$domains) {
                $domainModel = new DomainModel();
                $domainModel->domain = $domainNode->filter('domainname')->text();
                $domainModel->nsgroup = $domainNode->filter('nsgroup')->text();
                $domainModel->{'identity-registrant'} = $domainNode->filter('identity-registrant')->text();
                $domainModel->{'identity-admin'} = $domainNode->filter('identity-admin')->text();
                $domainModel->{'identity-tech'} = $domainNode->filter('identity-tech')->text();
                $domainModel->{'identity-billing'} = $domainNode->filter('identity-billing')->text();
                $domainModel->expire_date = $domainNode->filter('expire_date')->text();
                $domainModel->lock = $domainNode->filter('lock')->count()
                    ? $domainNode->filter('lock')->text()
                    : 'N';

                $domains[] = $domainModel;
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
            'domains' => $domains,
            ]
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

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_DOMAIN_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription
            );
        }

        $detailsNode = $xml->filter('channel > order > details');

        $domainModel = new DomainModel();
        foreach ($detailsNode->children() as $detailNode) {
            /** @var DOMElement $detailNode */
            $domainModel->{str_replace('-', '_', $detailNode->nodeName)} = $detailNode->textContent;
        }

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'domain' => $domainModel,
            ]
        );
    }

    /**
     * Register the domain.
     *
     * @throws OxxaException
     */
    public function register(DomainModel $domain): OxxaResult
    {
        if (! $domain->isUsable()) {
            return new OxxaResult(
                success: false,
                message: sprintf(
                    'The domain is missing the required fields: `%s`',
                    implode(', ', $domain->getMissingAttributes())
                )
            );
        }

        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'register'],
                $domain->toArray()
            ));

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_REGISTER_REQUESTED,
            message: $this->getStatusDescription($xml)
        );
    }

    /**
     * Determines the domain registration status.
     *
     * @throws OxxaException
     */
    public function registerStatus(DomainModel $domain): OxxaResult
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

        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'done' => $xml->filter('channel > order > done')->text() === 'TRUE',
                'description' => $statusDescription,
                'code' => $this->getStatusCode($xml),
            ]
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

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_TOKEN_SENT,
            message: $this->getStatusDescription($xml),
            data: [
                'epp' => $xml->filter('channel > order > details')->text(),
            ]
        );
    }

    /**
     * Update the domain. Be aware, when changing the identity-registrant some TLD's handle this as a transfer to a new
     * account. The TLD might charge you for that.
     *
     * @throws OxxaException
     */
    public function update(DomainModel $domain): OxxaResult
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

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_UPDATED,
            message: $this->getStatusDescription($xml)
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

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_AUTORENEW_CHANGED,
            message: $this->getStatusDescription($xml)
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

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_LOCK_CHANGED,
            message: $this->getStatusDescription($xml)
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
    public function updateNameservers(DomainModel $domain, bool $dnssecDelete = null): OxxaResult
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
        if (! is_null($domain->nsgroup)) {
            $parameters['nsgroup'] = $domain->nsgroup;
        }
        if (! is_null($domain->nsgroup)) {
            $parameters['handle'] = $domain->dnstemplate;
        }

        $xml = $this
            ->client
            ->request($parameters);

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_UPDATED,
            message: $this->getStatusDescription($xml)
        );
    }

    /**
     * Transfer the domain to the current account.
     *
     * @throws OxxaException
     */
    public function transfer(DomainModel $domain): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'transfer'],
                $domain->toArray()
            ));

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_TRANSFER_REQUESTED,
            message: $this->getStatusDescription($xml)
        );
    }

    /**
     * Determines the domain transfer status.
     *
     * @throws OxxaException
     */
    public function transferStatus(DomainModel $domain): OxxaResult
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

        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'done' => $xml->filter('channel > order > done')->text() === 'TRUE',
                'description' => $statusDescription,
                'code' => $this->getStatusCode($xml),
            ]
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

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_RESTORED,
            message: $this->getStatusDescription($xml)
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

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DOMAIN_DELETED,
            message: $this->getStatusDescription($xml)
        );
    }
}
