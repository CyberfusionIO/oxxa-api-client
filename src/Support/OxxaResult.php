<?php

namespace Cyberfusion\Oxxa\Support;

class OxxaResult
{
    public function __construct(
        private readonly bool $success = false,
        private readonly string $message = '',
        private readonly array $data = [],
        private readonly string $status = '',
    ) {
    }

    public function failed(): bool
    {
        return ! $this->success;
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getData(?string $key = null, mixed $fallback = null): mixed
    {
        if ($key !== null) {
            return $this->data[$key] ?? $fallback;
        }

        return $this->data;
    }
}
