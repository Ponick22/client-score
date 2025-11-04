<?php

namespace App\Domain\Profile\ValueObject;

use App\Domain\Profile\Exception\ProfileFirstNameInvalidException;

class ProfileFirstName
{
    private string $value;

    /**
     * @throws ProfileFirstNameInvalidException
     */
    public function __construct(string $firstName)
    {
        $firstName = trim($firstName);

        if (strlen($firstName) > 255) {
            throw new ProfileFirstNameInvalidException('error.first_name.too_long', ProfileFirstNameInvalidException::ERROR_TOO_LONG);
        }

        $this->value = $firstName;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
