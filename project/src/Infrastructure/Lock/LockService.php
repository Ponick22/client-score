<?php

namespace App\Infrastructure\Lock;

use App\Application\Lock\LockServiceInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class LockService implements LockServiceInterface
{
    /**
     * @var array<LockInterface>
     */
    private array $locks = [];

    public function __construct(
        private readonly LockFactory $lockFactory,
    ) {}

    public function acquire(string $resource, ?float $ttl = null): bool
    {
        $lock = $this->lockFactory->createLock($resource);
        if ($lock->acquire()) {
            $this->locks[$resource] = $lock;
            return true;
        }

        return false;
    }

    public function refresh(string $resource, ?float $ttl = null): void
    {
        if (isset($this->locks[$resource])) {
            $this->locks[$resource]->refresh($ttl);
        }
    }

    public function release(string $resource): void
    {
        if (isset($this->locks[$resource])) {
            $this->locks[$resource]->release();
            unset($this->locks[$resource]);
        }
    }
}
