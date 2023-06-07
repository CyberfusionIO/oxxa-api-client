<?php

namespace Cyberfusion\Oxxa\Requests;

class NameserverGroupListRequest extends Request
{
    protected array $available = [
        'nsgroup',
        'alias',
        'sortname',
        'sortorder',
        'globalsearch',
        'start',
        'records',
    ];
}
