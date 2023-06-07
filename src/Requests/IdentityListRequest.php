<?php

namespace Cyberfusion\Oxxa\Requests;

class IdentityListRequest extends Request
{
    protected array $available = [
        'handle',
        'name',
        'company_name',
        'alias',
        'sortname',
        'sortorder',
        'globalsearch',
        'start',
        'records',
    ];
}
