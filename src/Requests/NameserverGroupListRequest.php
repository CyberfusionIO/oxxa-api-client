<?php

namespace Cyberfusion\Oxxa\Requests;

use Cyberfusion\Oxxa\Contracts\Request;
use Cyberfusion\Oxxa\Support\ArrayHelper;

class NameserverGroupListRequest implements Request
{
    public function __construct(
        public ?string $nsgroup = null,
        public ?string $alias = null,
        public ?string $sortName = null,
        public ?string $sortOrder = null,
        public ?string $globalSearch = null,
        public ?int $start = null,
        public ?int $records = null,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'nsgroup' => $this->nsgroup,
            'alias' => $this->alias,
            'sortname' => $this->sortName,
            'sortorder' => $this->sortOrder,
            'globalsearch' => $this->globalSearch,
            'start' => $this->start,
            'records' => $this->records,
        ]);
    }
}
