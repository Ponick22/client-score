<?php

namespace App\Application\User;

use App\Application\User\Exception\UserEntityNotFoundException;
use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Repository\UserRepositoryInterface;

readonly class UserEntityGetter
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    /**
     * @throws UserEntityNotFoundException
     */
    public function get(int $id): UserEntityInterface
    {
        $entity = $this->repository->getOne($id);
        if (!$entity) {
            throw new UserEntityNotFoundException($id);
        }

        return $entity;
    }
}
