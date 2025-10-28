<?php

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\UserEmailInvalidException;

class UserEmail
{
    private string $value;

    /**
     * @throws UserEmailInvalidException
     */
    public function __construct(string $email)
    {
        $email = trim($email);

        if (strlen($email) > 255) {
            throw new UserEmailInvalidException('error.email.too_long');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UserEmailInvalidException('error.email.invalid');
        }

        $this->value = $email;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
