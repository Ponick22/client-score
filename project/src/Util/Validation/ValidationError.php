<?php

namespace App\Util\Validation;

readonly class ValidationError
{
    public function __construct(
        private string $property,
        private string $message
    ) {}

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function __toString(): string
    {
        return sprintf('%s: %s', $this->property, $this->message);
    }
}
