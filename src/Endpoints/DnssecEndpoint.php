<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\Dnssec;
use Cyberfusion\Oxxa\Support\OxxaResult;
use Symfony\Component\DomCrawler\Crawler;

class DnssecEndpoint extends Endpoint implements EndpointContract
{
    /**
     * Enable dnssec for the domain.
     *
     * @throws OxxaException
     */
    public function add(Dnssec $dnssec): OxxaResult
    {
        $requiredFields = [
            'sld',
            'tld',
            'flag',
            'protocol',
            'alg',
            'pubkey',
        ];

        if ($dnssec->missingAny($requiredFields)) {
            return new OxxaResult(
                success: false,
                message: sprintf(
                    'The model is missing the required fields: `%s`',
                    implode(', ', $dnssec->missingFields($requiredFields))
                )
            );
        }

        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'dnssec_add'],
                $dnssec->toArray()
            ));

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DNSSEC_ADDED,
            message: $this->getStatusDescription($xml)
        );
    }

    /**
     * Returns information about the domain.
     *
     * @throws OxxaException
     */
    public function info(string $sld, string $tld): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'dnssec_info',
                'sld' => $sld,
                'tld' => $tld,
            ]);

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_DNSSEC_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription
            );
        }

        $entries = [];
        $xml
            ->filter('channel > order > details > dnssec > key')
            ->each(function (Crawler $keyNode) use (&$entries, $sld, $tld) {
                $entries[] = new Dnssec(
                    sld: $sld,
                    tld: $tld,
                    flag: $keyNode->filter('flags')->text(),
                    protocol: $keyNode->filter('protocol')->text(),
                    alg: $keyNode->filter('alg')->text(),
                    publicKey: $keyNode->filter('pubKey')->text(),
                );
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'keys' => $entries,
            ]
        );
    }

    /**
     * Remove dnssec for the domain.
     *
     * @throws OxxaException
     */
    public function delete(Dnssec $dnssec): OxxaResult
    {
        $requiredFields = [
            'sld',
            'tld',
            'flag',
            'protocol',
            'alg',
            'pubkey',
        ];

        if ($dnssec->missingAny($requiredFields)) {
            return new OxxaResult(
                success: false,
                message: sprintf(
                    'The model is missing the required fields: `%s`',
                    implode(', ', $dnssec->missingFields($requiredFields))
                )
            );
        }

        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'dnssec_del'],
                $dnssec->toArray()
            ));

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_DNSSEC_DELETED,
            message: $this->getStatusDescription($xml)
        );
    }
}
