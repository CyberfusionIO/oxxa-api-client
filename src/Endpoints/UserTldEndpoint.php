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
        $parameters = [
            'command' => 'user_tld_list',
        ];
        if (! is_null($request)) {
            $parameters = array_merge($parameters, $request->serialize());
        }

        $xml = $this
            ->client
            ->request($parameters);

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_TLDS_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription
            );
        }

        $tlds = [];
        $xml
            ->filter('channel > order > details')
            ->children()
            ->each(function (Crawler $tldNode) use (&$tlds) {
                $tldModel = new Tld();
                $tldModel->tld = $tldNode->nodeName();
                $tldModel->register = $tldNode->filter('register')->count()
                    ? $tldNode->filter('register')->text()
                    : null;

                $transferNode = $tldNode->filter('transfer');
                $tldModel->transfer_lock = $transferNode->filter('lock')->count() && $transferNode->filter('lock')->text() === Toggle::ENABLED;
                $tldModel->transfer_epp = $transferNode->filter('epp')->count() && $transferNode->filter('epp')->text() === Toggle::ENABLED;

                $tldModel->renew = $tldNode->filter('renew')->count()
                    ? $tldNode->filter('renew')->text()
                    : null;
                $tldModel->redemption_period = (int) $tldNode->filter('redemption_period')->text();
                $tldModel->registrant = $tldNode->filter('registrant')->text() === Toggle::ENABLED;
                $tldModel->admin = $tldNode->filter('admin')->text() === Toggle::ENABLED;
                $tldModel->billing = $tldNode->filter('billing')->text() === Toggle::ENABLED;
                $tldModel->tech = $tldNode->filter('tech')->text() === Toggle::ENABLED;
                $tldModel->dnssec = $tldNode->filter('dnssec')->text() === Toggle::ENABLED;

                $priceNode = $tldNode->filter('price');
                $tldModel->price_register = $priceNode->filter('register')->count()
                    ? $priceNode->filter('register')->text()
                    : null;
                $tldModel->price_transfer = $priceNode->filter('transfer')->count()
                    ? $priceNode->filter('transfer')->text()
                    : null;
                $tldModel->price_renew = $priceNode->filter('renew')->count()
                    ? $priceNode->filter('renew')->text()
                    : null;
                $tldModel->price_restore = $priceNode->filter('domain_restore')->count()
                    ? $priceNode->filter('domain_restore')->text()
                    : null;
                $tldModel->price_trustee = $priceNode->filter('trustee')->count()
                    ? $priceNode->filter('trustee')->text()
                    : null;
                $tldModel->price_currency = $priceNode->filter('currency')->count()
                    ? $priceNode->filter('currency')->text()
                    : null;
                $tldModel->price_update = $priceNode->filter('domain_upd')->count()
                    ? $priceNode->filter('domain_upd')->text()
                    : null;

                $tlds[] = $tldModel;
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'tlds' => $tlds,
            ]
        );
    }
}
