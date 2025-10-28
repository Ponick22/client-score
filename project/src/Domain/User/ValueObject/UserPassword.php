<?php

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\UserPasswordInvalidException;

class UserPassword
{
    private string $value;

    /**
     * @throws UserPasswordInvalidException
     */
    public function __construct(string $password)
    {
        $password = trim($password);

        if (strlen($password) < 8) {
            throw new UserPasswordInvalidException('error.password.too_short');
        }

        if (strlen($password) > 128) {
            throw new UserPasswordInvalidException('error.password.too_long');
        }

        if (!preg_match("/[a-z]/", $password)) {
            throw new UserPasswordInvalidException('error.password.no_lowercase');
        }

        if (!preg_match("/[A-Z]/", $password)) {
            throw new UserPasswordInvalidException('error.password.no_uppercase');
        }

        if (!preg_match("/[0-9]/", $password)) {
            throw new UserPasswordInvalidException('error.password.no_number');
        }

        $this->value = $password;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
