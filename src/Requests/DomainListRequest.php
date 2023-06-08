<?php

namespace Cyberfusion\Oxxa\Requests;

use Cyberfusion\Oxxa\Contracts\Request;
use Cyberfusion\Oxxa\Support\ArrayHelper;

class DomainListRequest implements Request
{
    public function __construct(
        public ?string $sortName = null,
        public ?string $sortOrder = null,
        public ?int $start = null,
        public ?int $records = null,
        public ?string $sld = null,
        public ?string $tld = null,
        public ?string $nsgroup = null,
        public ?string $identity = null,
        public ?string $autoRenew = null,
        public ?string $lock = null,
        public ?string $expireDate = null,
        public ?string $status = null,
        public ?int $days = null,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'sortname' => $this->sortName,
            'sortorder' => $this->sortOrder,
            'start' => $this->start,
            'records' => $this->records,
            'sld' => $this->sld,
            'tld' => $this->tld,
            'nsgroup' => $this->nsgroup,
            'identity' => $this->identity,
            'autorenew' => $this->autoRenew,
            'lock' => $this->lock,
            'expire_date' => $this->expireDate,
            'status' => $this->status,
            'days' => $this->days,
        ]);
    }
}
