<?php

namespace Cyberfusion\Oxxa\Requests;

use Cyberfusion\Oxxa\Contracts\Request;
use Cyberfusion\Oxxa\Support\ArrayHelper;

class IdentityListRequest implements Request
{
    public function __construct(
        public ?string $handle = null,
        public ?string $name = null,
        public ?string $companyName = null,
        public ?string $alias = null,
        public ?string $sortName = null,
        public ?string $sortOrder = null,
        public ?bool $globalSearch = null,
        public ?int $start = null,
        public ?int $records = null,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'handle' => $this->handle,
            'name' => $this->name,
            'company_name' => $this->companyName,
            'alias' => $this->alias,
            'sortname' => $this->sortName,
            'sortorder' => $this->sortOrder,
            'globalsearch' => $this->globalSearch,
            'start' => $this->start,
            'records' => $this->records,
        ]);
    }
}
