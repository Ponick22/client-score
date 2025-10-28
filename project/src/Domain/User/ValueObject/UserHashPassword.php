<?php

namespace App\Domain\User\ValueObject;

class UserHashPassword
{
    private string $value;

    private function __construct(string $hashPassword)
    {
        $info = password_get_info($hashPassword);
        if ($info['algo'] === 0) {
            throw new \LogicException('Password is not hashed');
        }

        $this->value = $hashPassword;
    }

    public static function fromHashPassword(string $hashPassword): self
    {
        return new self($hashPassword);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
