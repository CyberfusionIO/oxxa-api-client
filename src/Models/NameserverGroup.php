<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model;
use Cyberfusion\Oxxa\Support\ArrayHelper;
use Cyberfusion\Oxxa\Traits\HasRequiredAttributes;

class NameserverGroup implements Model
{
    use HasRequiredAttributes;

    public function __construct(
        public ?string $handle = null,
        public ?string $alias = null,
        public ?string $nameserver1Fqdn = null,
        public ?string $nameserver2Fqdn = null,
        public ?string $nameserver3Fqdn = null,
        public ?string $nameserver4Fqdn = null,
        public ?string $nameserver5Fqdn = null,
        public ?string $nameserver6Fqdn = null,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'handle' => $this->handle,
            'alias' => $this->alias,
            'ns1_fqdn' => $this->nameserver1Fqdn,
            'ns2_fqdn' => $this->nameserver2Fqdn,
            'ns3_fqdn' => $this->nameserver3Fqdn,
            'ns4_fqdn' => $this->nameserver4Fqdn,
            'ns5_fqdn' => $this->nameserver5Fqdn,
            'ns6_fqdn' => $this->nameserver6Fqdn,
        ]);
    }
}
