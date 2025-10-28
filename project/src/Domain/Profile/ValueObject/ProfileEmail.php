<?php

namespace App\Domain\Profile\ValueObject;

use App\Domain\Profile\Exception\ProfileEmailInvalidException;

class ProfileEmail
{
    private string $value;

    /**
     * @throws ProfileEmailInvalidException
     */
    public function __construct(string $email)
    {
        $email = trim($email);

        if (strlen($email) > 255) {
            throw new ProfileEmailInvalidException('error.email.too_long');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ProfileEmailInvalidException('error.email.invalid');
        }

        $this->value = $email;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
