<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model as ModelContract;

class Tld extends Model implements ModelContract
{
    protected array $available = [
        'tld',
        'register',
        'transfer_lock',
        'transfer_epp',
        'renew',
        'redemption_period',
        'registrant',
        'admin',
        'billing',
        'tech',
        'dnssec',
        'price_register',
        'price_transfer',
        'price_renew',
        'price_currency',
        'price_trustee',
        'price_restore',
        'price_update',
    ];

    protected array $required = [
        'price_register',
        'price_transfer',
        'price_renew',
    ];
}
