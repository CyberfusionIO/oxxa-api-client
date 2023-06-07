<?php

namespace Cyberfusion\Oxxa\Requests;

class DomainListRequest extends Request
{
    protected array $available = [
        'sortname',
        'sortorder',
        'start',
        'records',
        'sld',
        'tld',
        'nsgroup',
        'identity',
        'autorenew',
        'lock',
        'expire_date',
        'status',
        'days',
    ];
}
