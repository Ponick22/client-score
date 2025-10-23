<?php

namespace App\Application\Client\Connector\Command\EntityUpdate\Validator;

use App\Application\Client\Connector\Command\EntityUpdate\Contract\ClientEntityUpdateDataInterface;
use App\Application\Profile\Validator\ProfileEntityPropertyValidator;
use App\Domain\Client\Entity\ClientEntityInterface;
use App\Util\Validation\ValidationErrorCollection;

readonly class ClientEntityUpdateValidator
{
    public function __construct(
        private ProfileEntityPropertyValidator $profileValidator,
    ) {}

    public function validate(ClientEntityInterface $entity, ClientEntityUpdateDataInterface $data): ValidationErrorCollection
    {
        $errors = new ValidationErrorCollection();

        $profile = $entity->getProfile();

        if ($profile->getEmail() !== $data->getEmail()) {
            $errors->addErrors($this->profileValidator->emailValidation($data->getEmail()));
        }

        if ($profile->getPhone() !== $data->getPhone()) {
            $errors->addErrors($this->profileValidator->phoneValidation($data->getPhone()));
        }

        return $errors;
    }
}
