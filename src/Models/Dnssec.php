<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model as ModelContract;

class Dnssec extends Model implements ModelContract
{
    protected array $available = [
        'sld',
        'tld',
        'flag',
        'protocol',
        'alg',
        'pubkey',
    ];

    protected array $required = [
        'sld',
        'tld',
        'flag',
        'protocol',
        'alg',
        'pubkey',
    ];

    public function toArray(): array
    {
        return $this->serialize();
    }
}
