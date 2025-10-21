<?php

namespace App\Application\Client\Validator;

use App\Application\Translator\ValidationErrorTranslatorInterface;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Profile\Repository\ProfileRepositoryInterface;
use App\Util\Validation\ValidationErrorCollection;

readonly class ClientEntityPropertyValidator
{
    public function __construct(
        private ClientRepositoryInterface          $repository,
        private ProfileRepositoryInterface         $profileRepository,
        private ValidationErrorTranslatorInterface $translator,
    ) {}

    public function profileValidation(int $profileId): ValidationErrorCollection
    {
        $property = 'profile';

        $errors = new ValidationErrorCollection();

        $profile = $this->profileRepository->getOne($profileId);
        if (!$profile) {
            $errors->addError($property, $this->t('error.profile.not_found'));
        } else if ($this->repository->getOneByProfile($profile)) {
            $errors->addError($property, $this->t('error.client.profile_already_exists'));
        }

        return $errors;
    }

    private function t(string $message): string
    {
        return $this->translator->translate($message);
    }
}
