<?php

namespace App\Application\User\Connector\Command\EntityCreation\Validator;

use App\Application\User\Connector\Command\EntityCreation\Contract\UserEntityCreationDataInterface;
use App\Application\User\Validator\UserEntityPropertyValidator;
use App\Util\Validation\ValidationErrorCollection;

readonly class UserEntityCreationValidator
{
    public function __construct(
        private UserEntityPropertyValidator $propertyValidator,
    ) {}

    public function validate(UserEntityCreationDataInterface $data): ValidationErrorCollection
    {
        $errors = new ValidationErrorCollection();

        $errors->addErrors($this->propertyValidator->emailValidation($data->getEmail()));

        return $errors;
    }
}
