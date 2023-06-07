<?php

namespace Cyberfusion\Oxxa\Requests;

use Cyberfusion\Oxxa\Traits\HasArrayableAttributes;
use Cyberfusion\Oxxa\Traits\HasAttributes;

abstract class Request
{
    use HasAttributes, HasArrayableAttributes;
}
