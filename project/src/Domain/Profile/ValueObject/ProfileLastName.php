<?php

namespace App\Domain\Profile\ValueObject;

use App\Domain\Profile\Exception\ProfileLastNameInvalidException;

class ProfileLastName
{
    private string $value;

    /**
     * @throws ProfileLastNameInvalidException
     */
    public function __construct(string $lastName)
    {
        $lastName = trim($lastName);

        if (strlen($lastName) > 255) {
            throw new ProfileLastNameInvalidException('error.last_name.too_long', ProfileLastNameInvalidException::ERROR_TOO_LONG);
        }

        $this->value = $lastName;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
