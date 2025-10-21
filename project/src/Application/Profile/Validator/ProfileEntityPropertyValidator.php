<?php

namespace App\Application\Profile\Validator;

use App\Application\Profile\Service\ProfilePhoneNormalizer;
use App\Application\Translator\ValidationErrorTranslatorInterface;
use App\Domain\Profile\Repository\ProfileRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Util\Validation\ValidationErrorCollection;

readonly class ProfileEntityPropertyValidator
{
    public function __construct(
        private UserRepositoryInterface            $userRepository,
        private ProfileRepositoryInterface         $repository,
        private ProfilePhoneNormalizer             $phoneNormalizer,
        private ValidationErrorTranslatorInterface $translator,
    ) {}

    public function userValidation(int $userId): ValidationErrorCollection
    {
        $property = 'user';

        $errors = new ValidationErrorCollection();

        $user = $this->userRepository->getOne($userId);
        if (!$user) {
            $errors->addError($property, $this->t('error.user.not_found'));
        } else if ($this->repository->getOneByUser($user)) {
            $errors->addError($property, $this->t('error.profile.user_already_exists'));
        }

        return $errors;
    }

    public function emailValidation(string $email): ValidationErrorCollection
    {
        $property = 'email';

        $errors = new ValidationErrorCollection();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors->addError($property, $this->t('error.email.invalid'));
        }

        return $errors;
    }

    public function phoneValidation(string $phone): ValidationErrorCollection
    {
        $property = 'phone';

        $errors = new ValidationErrorCollection();

        $phone = preg_replace('/\D+/', '', $phone);

        if (!preg_match('/^([87])\d{10}$/', $phone)) {
            $errors->addError($property, $this->t('error.phone.invalid'));
        } else if ($this->repository->getOneByPhone($this->phoneNormalizer->normalize($phone))) {
            $errors->addError($property, $this->t('error.phone.already_exists'));
        }

        return $errors;
    }

    private function t(string $message): string
    {
        return $this->translator->translate($message);
    }
}
