<?php

namespace Cyberfusion\Oxxa\Requests;

use Cyberfusion\Oxxa\Contracts\Request;
use Cyberfusion\Oxxa\Support\ArrayHelper;

class UserTldListRequest implements Request
{
    public function __construct(
        public ?bool $withPrice = null,
        public ?bool $converted = null,
        public ?string $tld = null,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'with_price' => $this->withPrice,
            'converted' => $this->converted,
            'tld' => $this->tld,
        ]);
    }
}
