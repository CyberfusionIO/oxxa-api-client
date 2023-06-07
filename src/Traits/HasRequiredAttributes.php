<?php

namespace Cyberfusion\Oxxa\Traits;

trait HasRequiredAttributes
{
    protected array $required = [];

    /**
     * Determines if all the required attributes are set.
     */
    public function isUsable(): bool
    {
        return count($this->getMissingAttributes()) === 0;
    }

    /**
     * Returns the misisng attributes in the model.
     */
    public function getMissingAttributes(): array
    {
        return array_diff($this->required, array_keys($this->attributes));
    }
}
