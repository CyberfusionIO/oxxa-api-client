<?php

namespace Cyberfusion\Oxxa\Requests;

class UserTldListRequest extends Request
{
    protected array $available = [
        'with_price',
        'converted',
        'tld',
    ];
}
