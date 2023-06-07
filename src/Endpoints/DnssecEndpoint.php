<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\Dnssec;
use Cyberfusion\Oxxa\Support\OxxaResult;
use DOMElement;
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
            ->filter('channel > order > details > key')
            ->each(function (Crawler $keyNode) use (&$entries) {
                $dnssecModel = new Dnssec();
                foreach ($keyNode->children() as $detailNode) {
                    /** @var DOMElement $detailNode */
                    $dnssecModel->{strtolower($detailNode->nodeName)} = $detailNode->textContent;
                }
                $entries[] = $dnssecModel;
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
