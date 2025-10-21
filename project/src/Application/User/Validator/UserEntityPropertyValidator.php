<?php

namespace App\Application\User\Validator;

use App\Application\Translator\ValidationErrorTranslatorInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Util\Validation\ValidationErrorCollection;

readonly class UserEntityPropertyValidator
{
    public function __construct(
        private UserRepositoryInterface            $repository,
        private ValidationErrorTranslatorInterface $translator,
    ) {}

    public function emailValidation(string $email): ValidationErrorCollection
    {
        $property = 'email';

        $errors = new ValidationErrorCollection();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors->addError($property, $this->t('error.email.invalid'));
        }

        if ($this->repository->getOneByEmail($email)) {
            $errors->addError($property, $this->t('error.email.already_exists'));
        }

        return $errors;
    }

    public function passwordValidation(string $password): ValidationErrorCollection
    {
        $property = 'password';

        $errors = new ValidationErrorCollection();

        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
            $errors->addError($property, $this->t('error.password.invalid'));
        }

        return $errors;
    }

    private function t(string $message): string
    {
        return $this->translator->translate($message);
    }
}
