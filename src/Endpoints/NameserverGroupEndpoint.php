<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\NameserverGroup;
use Cyberfusion\Oxxa\Requests\NameserverGroupListRequest;
use Cyberfusion\Oxxa\Support\OxxaResult;
use Symfony\Component\DomCrawler\Crawler;

class NameserverGroupEndpoint extends Endpoint implements EndpointContract
{
    /**
     * Returns all the available nameserver groups.
     *
     * @throws OxxaException
     */
    public function list(NameserverGroupListRequest $request = null): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'nsgroup_list'],
                $request?->toArray() ?? []
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_NSGROUPS_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        $nameserverGroups = [];
        $xml
            ->filter('channel > order > details > nsgroup')
            ->each(function (Crawler $nameserverGroupNode) use (&$nameserverGroups) {
                $nameserverGroups[] = new NameserverGroup(
                    handle: $nameserverGroupNode->filter('handle')->text(),
                    alias: $nameserverGroupNode->filter('alias')->text(),
                    nameserver1Fqdn: $nameserverGroupNode->filter('nameservers > ns1_fqdn')->text(),
                    nameserver2Fqdn: $nameserverGroupNode->filter('nameservers > ns2_fqdn')->text(),
                    nameserver3Fqdn: $nameserverGroupNode->filter('nameservers > ns3_fqdn')->count()
                        ? $nameserverGroupNode->filter('nameservers > ns3_fqdn')->text()
                        : null,
                    nameserver4Fqdn: $nameserverGroupNode->filter('nameservers > ns4_fqdn')->count()
                        ? $nameserverGroupNode->filter('nameservers > ns4_fqdn')->text()
                        : null,
                    nameserver5Fqdn: $nameserverGroupNode->filter('nameservers > ns5_fqdn')->count()
                        ? $nameserverGroupNode->filter('nameservers > ns5_fqdn')->text()
                        : null,
                    nameserver6Fqdn: $nameserverGroupNode->filter('nameservers > ns6_fqdn')->count()
                        ? $nameserverGroupNode->filter('nameservers > ns6_fqdn')->text()
                        : null,
                );
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'nameserverGroups' => $nameserverGroups,
            ],
            status: $statusCode,
        );
    }

    /**
     * Retrieve the nameserver group by the handle.
     *
     * @throws OxxaException
     */
    public function get(string $handle): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'nsgroup_get',
                'nsgroup' => $handle,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_NSGROUP_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        $detailsNode = $xml->filter('channel > order > details');

        $nameserverGroup = new NameserverGroup(
            handle: $handle,
            alias: $detailsNode->filter('alias')->text(),
            nameserver1Fqdn: $detailsNode->filter('ns1_fqdn')->text(),
            nameserver2Fqdn: $detailsNode->filter('ns2_fqdn')->text(),
            nameserver3Fqdn: $detailsNode->filter('ns3_fqdn')->count()
                ? $detailsNode->filter('ns3_fqdn')->text()
                : null,
            nameserver4Fqdn: $detailsNode->filter('ns4_fqdn')->count()
                ? $detailsNode->filter('ns4_fqdn')->text()
                : null,
            nameserver5Fqdn: $detailsNode->filter('ns5_fqdn')->count()
                ? $detailsNode->filter('ns5_fqdn')->text()
                : null,
            nameserver6Fqdn: $detailsNode->filter('ns6_fqdn')->count()
                ? $detailsNode->filter('ns6_fqdn')->text()
                : null,
        );

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'nameserverGroup' => $nameserverGroup,
            ],
            status: $statusCode,
        );
    }

    /**
     * Add the nameserver group.
     *
     * @throws OxxaException
     */
    public function create(NameserverGroup $nameserverGroup): OxxaResult
    {
        $requiredFields = [
            'ns1_fqdn',
            'ns2_fqdn',
        ];

        if ($nameserverGroup->missingAny($requiredFields)) {
            return new OxxaResult(
                success: false,
                message: sprintf(
                    'The nameserver group is missing the required fields: `%s`',
                    implode(', ', $nameserverGroup->missingFields($requiredFields))
                )
            );
        }

        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'nsgroup_add'],
                $nameserverGroup->toArray()
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_NSGROUP_ADDED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'handle' => $xml->filter('channel > order > details')->text(),
            ],
            status: $statusCode,
        );
    }

    /**
     * Update the nameserver group.
     *
     * @throws OxxaException
     */
    public function update(string $handle, NameserverGroup $nameserverGroup): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                [
                    'command' => 'nsgroup_upd',
                    'nsgroup' => $handle,
                ],
                $nameserverGroup->toArray()
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_NSGROUP_UPDATED,
            message: $statusDescription,
            status: $statusCode,
        );
    }

    /**
     * Delete the nameserver group.
     *
     * @throws OxxaException
     */
    public function delete(string $handle): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'nsgroup_del',
                'nsgroup' => $handle,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_NSGROUP_DELETED,
            message: $statusDescription,
            status: $statusCode,
        );
    }
}
