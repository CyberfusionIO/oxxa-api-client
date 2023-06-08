<?php

namespace Cyberfusion\Oxxa\Support;

use Cyberfusion\Oxxa\Enum\Toggle;
use DateTimeInterface;

class ArrayHelper
{
    public static function transformToParameters(array $array): array
    {
        $parameters = [];

        foreach ($array as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            // Turn booleans into the Y/N toggle
            if (is_bool($value)) {
                $value = Toggle::fromBoolean($value);
            }

            if ($value instanceof DateTimeInterface) {
                $value = $value->format('Y-m-d');
            }

            $parameters[$key] = $value;
        }

        return $parameters;
    }
}
