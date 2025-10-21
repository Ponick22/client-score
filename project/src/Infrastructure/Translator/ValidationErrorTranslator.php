<?php

namespace App\Infrastructure\Translator;

use App\Application\Translator\ValidationErrorTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ValidationErrorTranslator implements ValidationErrorTranslatorInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {}

    public function translate(string $message): string
    {
        return $this->translator->trans($message, [], 'validators');
    }
}
