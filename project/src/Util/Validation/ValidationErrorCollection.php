<?php

namespace App\Util\Validation;

use App\Util\Collection\ArrayCollectionAbstract;

/**
 * @extends ArrayCollectionAbstract<ValidationError>
 * @method ValidationError offsetGet()
 * @method add(ValidationError $value)
 */
class ValidationErrorCollection extends ArrayCollectionAbstract
{
    public function getClass(): string
    {
        return ValidationError::class;
    }

    public function addError(string $property, string $message): self
    {
        $this->add(new ValidationError($property, $message));

        return $this;
    }

    public function addErrors(ValidationErrorCollection $errors): self
    {
        foreach ($errors as $error) {
            $this->add($error);
        }

        return $this;
    }

    public function isValid(): bool
    {
        return $this->count() === 0;
    }
}
