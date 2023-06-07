<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\NameserverGroup as NameserverGroupModel;
use Cyberfusion\Oxxa\Requests\NameserverGroupListRequest;
use Cyberfusion\Oxxa\Support\OxxaResult;
use DOMElement;
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
        $parameters = [
            'command' => 'nsgroup_list',
        ];
        if (! is_null($request)) {
            $parameters = array_merge($parameters, $request->serialize());
        }

        $xml = $this
            ->client
            ->request($parameters);

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_NSGROUPS_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription
            );
        }

        $nameserverGroups = [];
        $xml
            ->filter('channel > order > details > nsgroup')
            ->each(function (Crawler $nameserverGroupNode) use (&$nameserverGroups) {
                $nameserverGroupModel = new NameserverGroupModel();
                $nameserverGroupModel->handle = $nameserverGroupNode->filter('handle')->text();
                $nameserverGroupModel->alias = $nameserverGroupNode->filter('name')->text();

                $nameserverGroups[] = $nameserverGroupModel;
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'nameserverGroups' => $nameserverGroups,
            ]
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

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_NSGROUP_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription
            );
        }

        $detailsNode = $xml->filter('channel > order > details');

        $nameserverGroupModel = new NameserverGroupModel();
        foreach ($detailsNode->children() as $detailNode) {
            /** @var DOMElement $detailNode */
            $nameserverGroupModel->{$detailNode->nodeName} = $detailNode->textContent;
        }

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'nameserverGroup' => $nameserverGroupModel,
            ]
        );
    }

    /**
     * Add the nameserver group.
     *
     * @throws OxxaException
     */
    public function create(NameserverGroupModel $nameserverGroup): OxxaResult
    {
        if (! $nameserverGroup->isUsable()) {
            return new OxxaResult(
                false,
                sprintf(
                    'The nameserver group is missing the required fields: `%s`',
                    implode(', ', $nameserverGroup->getMissingAttributes())
                )
            );
        }

        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'nsgroup_add'],
                $nameserverGroup->serialize()
            ));

        $statusDescription = $this->getStatusDescription($xml);
        if ($this->getStatusCode($xml) !== StatusCode::STATUS_NSGROUP_ADDED) {
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
     * Update the nameserver group.
     *
     * @throws OxxaException
     */
    public function update(string $handle, NameserverGroupModel $nameserverGroup): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                [
                    'command' => 'nsgroup_upd',
                    'nsgroup' => $handle,
                ],
                $nameserverGroup->serialize()
            ));

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_NSGROUP_UPDATED,
            message: $this->getStatusDescription($xml)
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

        return new OxxaResult(
            success: $this->getStatusCode($xml) === StatusCode::STATUS_NSGROUP_DELETED,
            message: $this->getStatusDescription($xml)
        );
    }
}
