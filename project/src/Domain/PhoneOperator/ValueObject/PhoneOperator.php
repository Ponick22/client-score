<?php

namespace App\Domain\PhoneOperator\ValueObject;

class PhoneOperator
{
    private string $value;

    private function __construct(string $operator)
    {
        $operator = trim($operator);

        $this->value = $operator;
    }

    public static function fromPhoneOperator(string $operator): self
    {
        return new self($operator);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
