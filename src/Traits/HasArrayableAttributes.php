<?php

namespace Cyberfusion\Oxxa\Traits;

use Cyberfusion\Oxxa\Enum\Toggle;
use DateTimeInterface;

trait HasArrayableAttributes
{
    /**
     * Serializes the attributes to an array.
     */
    public function serialize(array $fieldConfiguration = []): array
    {
        $array = [];

        foreach ($this->attributes as $key => $value) {
            // Transform the keys when required
            if (array_key_exists($key, $fieldConfiguration)) {
                $key = $fieldConfiguration[$key];
            }

            // Turn booleans into the Y/N toggle
            if (is_bool($value)) {
                $value = Toggle::fromBoolean($value);
            }

            if ($value instanceof DateTimeInterface) {
                $value = $value->format('Y-m-d');
            }

            $array[$key] = $value;
        }

        return $array;
    }
}
