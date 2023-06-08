<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model;
use Cyberfusion\Oxxa\Support\ArrayHelper;
use Cyberfusion\Oxxa\Traits\HasRequiredAttributes;

class Dnssec implements Model
{
    use HasRequiredAttributes;

    public function __construct(
        public ?string $sld = null,
        public ?string $tld = null,
        public ?string $flag = null,
        public ?string $protocol = null,
        public ?string $alg = null,
        public ?string $publicKey = null,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'sld' => $this->sld,
            'tld' => $this->tld,
            'flag' => $this->flag,
            'protocol' => $this->protocol,
            'alg' => $this->alg,
            'pubkey' => $this->publicKey,
        ]);
    }
}
