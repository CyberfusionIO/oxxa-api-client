<?php

namespace Cyberfusion\Oxxa\Enum;

class Toggle
{
    public const ENABLED = 'Y';

    public const DISABLED = 'N';

    public static function fromBoolean(bool $toggle): string
    {
        return $toggle
            ? self::ENABLED
            : self::DISABLED;
    }
}
