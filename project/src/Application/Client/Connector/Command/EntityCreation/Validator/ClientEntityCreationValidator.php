<?php

namespace App\Application\Client\Connector\Command\EntityCreation\Validator;

use App\Application\Client\Connector\Command\EntityCreation\Contract\ClientEntityCreationDataInterface;
use App\Application\Profile\Validator\ProfileEntityPropertyValidator;
use App\Application\User\Validator\UserEntityPropertyValidator;
use App\Util\Validation\ValidationErrorCollection;

readonly class ClientEntityCreationValidator
{
    public function __construct(
        private UserEntityPropertyValidator    $userValidator,
        private ProfileEntityPropertyValidator $profileValidator,
    ) {}

    public function validate(ClientEntityCreationDataInterface $data): ValidationErrorCollection
    {
        $errors = new ValidationErrorCollection();

        $result = $this->profileValidator->emailValidation($data->getEmail());
        if ($result->isValid()) {
            $result = $this->userValidator->emailValidation($data->getEmail());
        }
        $errors->addErrors($result);

        $errors->addErrors($this->profileValidator->phoneValidation($data->getPhone()));

        return $errors;
    }

}
