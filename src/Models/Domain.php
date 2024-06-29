<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model;
use Cyberfusion\Oxxa\Support\ArrayHelper;
use Cyberfusion\Oxxa\Traits\HasRequiredAttributes;
use DateTimeInterface;

class Domain implements Model
{
    use HasRequiredAttributes;

    public function __construct(
        public ?string $identityAdmin = null,
        public ?string $identityTech = null,
        public ?string $identityBilling = null,
        public ?string $identityRegistrant = null,
        public ?string $identityReseller = null,
        public ?string $nameserverGroup = null,
        public ?string $dnsTemplate = null,
        public ?string $domain = null,
        public ?string $sld = null,
        public ?string $tld = null,
        public ?int $period = 1,
        public ?bool $autoRenew = false,
        public ?DateTimeInterface $expireDate = null,
        public ?bool $lock = null,
        public ?bool $useTrustee = null,
        public ?bool $dnsSec = null,
        public ?string $premiumPrice = null,
        public ?DateTimeInterface $executionAt = null,
        public ?string $transferCode = null,
        public ?bool $dnssecDelete = null,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'identity-admin' => $this->identityAdmin,
            'identity-tech' => $this->identityTech,
            'identity-billing' => $this->identityBilling,
            'identity-registrant' => $this->identityRegistrant,
            'identity-reseller' => $this->identityReseller,
            'nsgroup' => $this->nameserverGroup,
            'dnstemplate' => $this->dnsTemplate,
            'sld' => $this->sld,
            'tld' => $this->tld,
            'period' => $this->period,
            'autorenew' => $this->autoRenew,
            'lock' => $this->lock,
            'usetrustee' => $this->useTrustee,
            'premium_price' => $this->premiumPrice,
            'execution_at' => $this
                ->executionAt
                ?->format('d-m-Y'),
            'trans_epp' => $this->transferCode,
            'dnssec_delete' => $this->dnssecDelete,
        ]);
    }
}
