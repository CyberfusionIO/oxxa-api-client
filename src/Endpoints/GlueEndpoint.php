<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Support\OxxaResult;

class GlueEndpoint extends Endpoint implements EndpointContract
{
    /**
     * Stores the ip's for the nameserver.
     *
     * @throws OxxaException
     */
    public function put(string $fqdn, array $ips = []): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'glue',
                'ns_fqdn' => $fqdn,
                'glues' => $ips,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_GLUES_UPDATED) {
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
                'fqdn' => $fqdn,
                'ips' => $ips,
            ],
            status: $statusCode,
        );
    }
}
