<?php

namespace App\Application\Profile\Validator;

use App\Domain\Profile\Repository\ProfileRepositoryInterface;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Util\Validation\ValidationErrorCollection;

readonly class ProfileEntityPropertyValidator
{
    public function __construct(
        private UserRepositoryInterface    $userRepository,
        private ProfileRepositoryInterface $repository,
    ) {}

    public function userValidation(int $userId): ValidationErrorCollection
    {
        $property = 'user';

        $errors = new ValidationErrorCollection();

        $user = $this->userRepository->getOne($userId);
        if (!$user) {
            $errors->addError($property, 'error.user.not_found');
        } else if ($this->repository->getOneByUser($user)) {
            $errors->addError($property, 'error.profile.user_already_exists');
        }

        return $errors;
    }

    public function emailValidation(ProfileEmail $email): ValidationErrorCollection
    {
        $property = 'email';

        $errors = new ValidationErrorCollection();

        if ($this->repository->getOneByEmail($email)) {
            $errors->addError($property, 'error.email.already_exists');
        }

        return $errors;
    }

    public function phoneValidation(ProfilePhone $phone): ValidationErrorCollection
    {
        $property = 'phone';

        $errors = new ValidationErrorCollection();

        if ($this->repository->getOneByPhone($phone)) {
            $errors->addError($property, 'error.phone.already_exists');
        }

        return $errors;
    }
}
