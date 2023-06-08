<?php

namespace Cyberfusion\Oxxa\Traits;

trait HasRequiredAttributes
{
    /**
     * Determines if all the fields are set.
     */
    public function filledAll(array $fields = []): bool
    {
        return count($this->missingFields($fields)) === 0;
    }

    public function missingAny(array $fields = []): bool
    {
        return count($this->missingFields($fields)) !== 0;
    }

    /**
     * Returns the missing fields in the model.
     */
    public function missingFields(array $fields = []): array
    {
        return array_diff($fields, array_keys($this->toArray()));
    }
}
