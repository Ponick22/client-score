<?php

namespace App\Application\Exception;

use App\Util\Validation\ValidationErrorCollection;

class ValidationException extends ApplicationExceptionAbstract
{
    public function __construct(protected readonly ValidationErrorCollection $errors)
    {
        parent::__construct(sprintf('Validation failed with %s errors', $errors->count()));
    }

    public function getErrors(): ValidationErrorCollection
    {
        return $this->errors;
    }
}
