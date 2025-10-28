<?php

namespace App\Application\User\Validator;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserEmail;
use App\Util\Validation\ValidationErrorCollection;

readonly class UserEntityPropertyValidator
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    public function emailValidation(UserEmail $email): ValidationErrorCollection
    {
        $property = 'email';

        $errors = new ValidationErrorCollection();

        if ($this->repository->getOneByEmail($email)) {
            $errors->addError($property, 'error.email.already_exists');
        }

        return $errors;
    }
}
