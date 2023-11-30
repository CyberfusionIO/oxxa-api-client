<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Enum\Toggle;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\Tld;
use Cyberfusion\Oxxa\Requests\UserTldListRequest;
use Cyberfusion\Oxxa\Support\OxxaResult;
use Symfony\Component\DomCrawler\Crawler;

class UserTldEndpoint extends Endpoint implements EndpointContract
{
    /**
     * Returns all the available identities.
     *
     * @throws OxxaException
     */
    public function list(UserTldListRequest $request = null): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'user_tld_list'],
                $request?->toArray() ?? []
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_TLDS_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        $tlds = [];
        $xml
            ->filter('channel > order > details')
            ->children()
            ->each(function (Crawler $tldNode) use (&$tlds) {
                $transferNode = $tldNode->filter('transfer');
                $priceNode = $tldNode->filter('price');

                $tlds[] = new Tld(
                    tld: $tldNode->nodeName(),
                    register: $tldNode->filter('register')->count()
                        ? $tldNode->filter('register')->text()
                        : null,
                    transferLock: $transferNode->filter('lock')->count() && $transferNode->filter('lock')->text() === Toggle::ENABLED,
                    transferEpp: $transferNode->filter('epp')->count() && $transferNode->filter('epp')->text() === Toggle::ENABLED,
                    renew: $tldNode->filter('renew')->count()
                        ? $tldNode->filter('renew')->text()
                        : null,
                    redemptionPeriod: (int) $tldNode->filter('redemption_period')->text(),
                    registrant: $tldNode->filter('registrant')->text() === Toggle::ENABLED,
                    admin: $tldNode->filter('admin')->text() === Toggle::ENABLED,
                    billing: $tldNode->filter('billing')->text() === Toggle::ENABLED,
                    tech: $tldNode->filter('tech')->text() === Toggle::ENABLED,
                    dnssec: $tldNode->filter('dnssec')->text() === Toggle::ENABLED,
                    reseller: $tldNode->filter('reseller')->text() === Toggle::ENABLED,
                    priceRegister: $priceNode->filter('register')->count()
                        ? $priceNode->filter('register')->text()
                        : null,
                    priceTransfer: $priceNode->filter('transfer')->count()
                        ? $priceNode->filter('transfer')->text()
                        : null,
                    priceRenew: $priceNode->filter('renew')->count()
                        ? $priceNode->filter('renew')->text()
                        : null,
                    priceCurrency: $priceNode->filter('currency')->count()
                        ? $priceNode->filter('currency')->text()
                        : null,
                    priceTrustee: $priceNode->filter('trustee')->count()
                        ? $priceNode->filter('trustee')->text()
                        : null,
                    priceRestore: $priceNode->filter('domain_restore')->count()
                        ? $priceNode->filter('domain_restore')->text()
                        : null,
                    priceUpdate: $priceNode->filter('domain_upd')->count()
                        ? $priceNode->filter('domain_upd')->text()
                        : null,
                );
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'tlds' => $tlds,
            ],
            status: $statusCode,
        );
    }
}
