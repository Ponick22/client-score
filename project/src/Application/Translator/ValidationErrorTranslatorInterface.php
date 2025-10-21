<?php

namespace App\Application\Translator;

interface ValidationErrorTranslatorInterface
{
    public function translate(string $message): string;
}
