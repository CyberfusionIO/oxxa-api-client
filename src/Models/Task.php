<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model;
use Cyberfusion\Oxxa\Support\ArrayHelper;
use Cyberfusion\Oxxa\Traits\HasRequiredAttributes;
use DateTimeInterface;

class Task implements Model
{
    use HasRequiredAttributes;

    public function __construct(
        public ?int $id = null,
        public ?string $sld = null,
        public ?string $tld = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?DateTimeInterface $dateTime = null,
        public array $domains = [],
        public ?string $type = null,
        public ?string $info = null,
    ) {
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'id' => $this->id,
            'sld' => $this->sld,
            'tld' => $this->tld,
            'tasktitle' => $this->title,
            'description' => $this->description,
            'datetime' => $this->dateTime?->format('Y-m-d H:i:s'),
            'domains' => implode(',', $this->domains),
            'type' => $this->type,
            'info' => $this->info,
        ]);
    }
}
