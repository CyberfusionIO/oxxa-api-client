<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model;
use Cyberfusion\Oxxa\Support\ArrayHelper;

class SslProduct implements Model
{
    public function __construct(
        public int $id,
        public string $name,
        public string $type,
        public array $pricing,
        public bool $wildcard,
        public bool $priceExtraDomain,
        public bool $cnameAuth,
        public int $warranty,
        public bool $greenBar,
        public string $vendor,
        public string $deliveryTime,
        public int $amountDomains,
        public int $amountMaxDomains,
        public string $info,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'id' => $this->id,
            'name' => $this->name,
            'pricing' => $this->pricing,
            'type' => $this->type,
            'wildcard' => $this->wildcard,
            'price_extra_domain' => $this->priceExtraDomain,
            'cname_auth' => $this->cnameAuth,
            'warranty' => $this->warranty,
            'greenbar' => $this->greenBar,
            'vendor' => $this->vendor,
            'delivery_time' => $this->deliveryTime,
            'amount_domains' => $this->amountDomains,
            'amount_max_domains' => $this->amountMaxDomains,
            'info' => $this->info,
        ]);
    }
}
