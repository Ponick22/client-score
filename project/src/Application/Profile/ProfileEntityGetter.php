<?php

namespace App\Application\Profile;

use App\Application\Profile\Exception\ProfileEntityNotFoundException;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Repository\ProfileRepositoryInterface;

readonly class ProfileEntityGetter
{
    public function __construct(
        private ProfileRepositoryInterface $repository,
    ) {}

    /**
     * @throws ProfileEntityNotFoundException
     */
    public function get(int $id): ProfileEntityInterface
    {
        $entity = $this->repository->getOne($id);
        if (!$entity) {
            throw new ProfileEntityNotFoundException($id);
        }

        return $entity;
    }
}
