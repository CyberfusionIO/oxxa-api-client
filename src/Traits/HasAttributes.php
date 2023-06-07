<?php

namespace Cyberfusion\Oxxa\Traits;

use Illuminate\Support\Str;

trait HasAttributes
{
    /**
     * Holds the attributes.
     */
    protected array $attributes = [];

    /**
     * Holds the list of available attributes.
     */
    protected array $available = [];

    /**
     * Returns the attributes. When the attributes isn't set, it returns null.
     */
    public function __get(string $name): mixed
    {
        $method = 'get'.Str::studly($name).'Attribute';
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return $this->attributes[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Sets the attributes. When the attribute isn't in the list of available attributes, its value won't be stored.
     */
    public function __set(string $name, mixed $value): void
    {
        $method = 'set'.Str::studly($name).'Attribute';
        if (method_exists($this, $method)) {
            $this->{$method}($value);

            return;
        }

        if (in_array($name, $this->available, true)) {
            $this->attributes[$name] = $value;
        }
    }
}
