<?php

namespace App\Application\Profile\Service;

class ProfilePhoneNormalizer
{
    public function normalize(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (strlen($phone) === 11) {
            if ($phone[0] === '8') {
                $phone = '7' . substr($phone, 1);
            }

            if ($phone[0] === '7') {
                $phone = '+' . $phone;
            }
        } else if (strlen($phone) === 10) {
            $phone = '+7' . $phone;
        }

        return $phone;
    }
}
