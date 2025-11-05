<?php

namespace App\Tests\Traits;

use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;

trait ProfileDataTrait
{
    private ProfileEmail     $profileEmail;
    private ProfilePhone     $phone;
    private ProfileFirstName $firstName;
    private ProfileLastName  $lastName;

    private function initProfileData(): void
    {
        $this->profileEmail = new ProfileEmail('profile@example.com');
        $this->phone        = new ProfilePhone('+79123456789');
        $this->firstName    = new ProfileFirstName('First');
        $this->lastName     = new ProfileLastName('Last');
    }
}
