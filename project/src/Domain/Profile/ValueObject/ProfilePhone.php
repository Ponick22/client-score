<?php

namespace App\Domain\Profile\ValueObject;

use App\Domain\Profile\Exception\ProfilePhoneInvalidException;

class ProfilePhone
{
    private string $value;

    /**
     * @throws ProfilePhoneInvalidException
     */
    public function __construct(string $phone)
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (strlen($phone) === 11) {
            if ($phone[0] === '8') {
                $phone = '7' . substr($phone, 1);
            }

            if ($phone[0] === '7') {
                $phone = '+' . $phone;
            }
        } else {
            throw new ProfilePhoneInvalidException('error.phone.invalid');
        }

        $this->value = $phone;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
