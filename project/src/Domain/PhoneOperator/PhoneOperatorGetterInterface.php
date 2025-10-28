<?php

namespace App\Domain\PhoneOperator;

use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;

interface PhoneOperatorGetterInterface
{
    /**
     * @throws PhoneOperatorException
     */
    public function get(string $phone): ?PhoneOperator;
}
