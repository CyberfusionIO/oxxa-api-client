<?php

namespace Cyberfusion\Oxxa\DataTransferObjects;

class Price
{
    public function __construct(
        public int $period,
        public float $price,
    ) {
    }
}
