<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Traits\HasArrayableAttributes;
use Cyberfusion\Oxxa\Traits\HasAttributes;
use Cyberfusion\Oxxa\Traits\HasRequiredAttributes;

abstract class Model
{
    use HasAttributes, HasRequiredAttributes, HasArrayableAttributes;
}
