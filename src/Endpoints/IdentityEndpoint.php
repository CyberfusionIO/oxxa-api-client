<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\Identity as IdentityModel;
use Cyberfusion\Oxxa\Requests\IdentityListRequest;
use Cyberfusion\Oxxa\Support\OxxaResult;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

class IdentityEndpoint extends Endpoint implements EndpointContract
{
    /**
     * Returns all the available identities.
     *
     * @throws OxxaException
     */
    public function list(IdentityListRequest $request = null): OxxaResult
    {
        $parameters = [
            'command' => 'identity_list',
        ];
        if (! is_null($request)) {
            $parameters = array_merge($parameters, $request->serialize());
        }

        $xml = $this
            ->client
            ->request($parameters);

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_IDENTITIES_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription
            );
        }

        $identities = [];
        $xml
            ->filter('channel > order > details > identity')
            ->each(function (Crawler $identityNode) use (&$identities) {
                $identityModel = new IdentityModel();
                $identityModel->handle = $identityNode->filter('handle')->text();
                $identityModel->alias = $identityNode->filter('alias')->text();
                $identityModel->company_name = $identityNode->filter('company_name')->text();
                $identityModel->name = $identityNode->filter('name')->text();

                $identities[] = $identityModel;
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'identities' => $identities,
            ]
        );
    }

    /**
     * Retrieve the identity by the handle.
     *
     * @throws OxxaException
     */
    public function get(string $handle): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'identity_get',
                'identity' => $handle,
            ]);

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_IDENTITY_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription
            );
        }

        $detailsNode = $xml->filter('channel > order > details');

        $identityModel = new IdentityModel();
        foreach ($detailsNode->children() as $detailNode) {
            /** @var DOMElement $detailNode */
            $identityModel->{$detailNode->nodeName} = $detailNode->textContent;
        }

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'identity' => $identityModel,
            ]
        );
    }

    /**
     * Create the identity.
     *
     * @throws OxxaException
     */
    public function create(IdentityModel $identity): OxxaResult
    {
        if (! $identity->isUsable()) {
            return new OxxaResult(
                success: false,
                message: sprintf(
                    'The identity is missing the required fields: `%s`',
                    implode(', ', $identity->getMissingAttributes())
                )
            );
        }

        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'identity_add'],
                $identity->serialize()
            ));

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_IDENTITY_ADDED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription
            );
        }

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'handle' => $xml->filter('channel > order > details')->text(),
            ]
        );
    }

    /**
     * Update the identity.
     *
     * @throws OxxaException
     */
    public function update(string $handle, IdentityModel $identity): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                [
                    'command' => 'identity_upd',
                    'handle' => $handle,
                ],
                $identity->serialize()
            ));

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_IDENTITY_UPDATED,
            message: $this->getStatusDescription($xml)
        );
    }

    /**
     * Remove the identity.
     *
     * @throws OxxaException
     */
    public function delete(string $handle): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'identity_del',
                'identity' => $handle,
            ]);

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_IDENTITY_DELETED,
            message: $this->getStatusDescription($xml)
        );
    }
}
