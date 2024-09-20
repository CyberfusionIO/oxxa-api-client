<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Oxxa;
use Cyberfusion\Oxxa\Traits\ResponseParser;

abstract class Endpoint
{
    use ResponseParser;

    public function __construct(protected Oxxa $client)
    {
    }
}
