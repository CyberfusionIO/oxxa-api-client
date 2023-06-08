<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model;
use Cyberfusion\Oxxa\Support\ArrayHelper;
use Cyberfusion\Oxxa\Traits\HasRequiredAttributes;

class Tld implements Model
{
    use HasRequiredAttributes;

    protected array $required = [
        'price_register',
        'price_transfer',
        'price_renew',
    ];

    public function __construct(
        public ?string $tld = null,
        public ?string $register = null,
        public ?bool $transferLock = null,
        public ?bool $transferEpp = null,
        public ?string $renew = null,
        public ?int $redemptionPeriod = null,
        public ?bool $registrant = null,
        public ?bool $admin = null,
        public ?bool $billing = null,
        public ?bool $tech = null,
        public ?bool $dnssec = null,
        public ?bool $reseller = null,
        public ?string $priceRegister = null,
        public ?string $priceTransfer = null,
        public ?string $priceRenew = null,
        public ?string $priceCurrency = null,
        public ?string $priceTrustee = null,
        public ?string $priceRestore = null,
        public ?string $priceUpdate = null,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'tld' => $this->tld,
            'register' => $this->register,
            'transfer_lock' => $this->transferLock,
            'transfer_epp' => $this->transferEpp,
            'renew' => $this->renew,
            'redemption_period' => $this->redemptionPeriod,
            'registrant' => $this->registrant,
            'admin' => $this->admin,
            'billing' => $this->billing,
            'tech' => $this->tech,
            'dnssec' => $this->dnssec,
            'price_register' => $this->priceRegister,
            'price_transfer' => $this->priceTransfer,
            'price_renew' => $this->priceRenew,
            'price_currency' => $this->priceCurrency,
            'price_trustee' => $this->priceTrustee,
            'price_restore' => $this->priceRestore,
            'price_update' => $this->priceUpdate,
        ]);
    }
}
