<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model as ModelContract;

class Domain extends Model implements ModelContract
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_QUARANTINE = 'quarantine';

    public const STATUS_DELETE = 'delete';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_TRANSFERRED = 'transferred';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_RENEWED = 'renewed';

    public const STATUS_RENEW = 'renew';

    protected array $available = [
        'identity-admin',
        'identity-tech',
        'identity-billing',
        'identity-registrant',
        'identity-reseller',
        'nsgroup',
        'dnstemplate',
        'domain',
        'sld',
        'tld',
        'period',
        'autorenew',
        'lock',
        'use_trustee',
        'premium_price',
        'execution_at',
    ];

    protected array $required = [
        'identity-admin',
        'identity-registrant',
        'nsgroup',
        'sld',
        'tld',
        'period',
        'autorenew',
    ];

    public function __construct()
    {
        // Default values
        $this->autorenew = 'N';
        $this->period = 1;
    }

    public function getDomainAttribute(): string
    {
        if (array_key_exists('domain', $this->attributes)) {
            return $this->attributes['domain'];
        }

        return $this->sld.'.'.$this->tld;
    }

    public function setDomainAttribute(string $domain): void
    {
        $this->attributes['domain'] = $domain;
    }

    public function toArray(): array
    {
        return $this->serialize([
            'identity-admin' => 'identity-admin',
            'identity-tech' => 'identity-tech',
            'identity-billing' => 'identity-billing',
            'identity-registrant' => 'identity-registrant',
            'identity-reseller' => 'identity-reseller',
        ]);
    }
}
