<?php

namespace App\Application\Client\Service;

use App\Application\PhoneOperator\Exception\PhoneOperatorException;
use App\Application\PhoneOperator\PhoneOperatorGetterInterface;
use App\Domain\Client\Entity\ClientEntityInterface;

readonly class ClientPhoneOperatorChanger
{
    public function __construct(
        private PhoneOperatorGetterInterface $operatorGetter,
    ) {}

    /**
     * @throws PhoneOperatorException
     */
    public function change(ClientEntityInterface $entity, string $phone): void
    {
        $operator = $this->operatorGetter->get($phone);

        $entity->changePhoneOperator($operator);
    }
}
