<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model as ModelContract;

class NameserverGroup extends Model implements ModelContract
{
    protected array $available = [
        'handle',
        'alias',
        'ns1_fqdn',
        'ns2_fqdn',
        'ns3_fqdn',
        'ns4_fqdn',
        'ns5_fqdn',
        'ns6_fqdn',
    ];

    protected array $required = [
        'ns1_fqdn',
        'ns2_fqdn',
    ];
}
