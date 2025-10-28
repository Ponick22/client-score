<?php

namespace App\Application\Lock;

interface LockServiceInterface
{
    public function acquire(string $resource, ?float $ttl = null): bool;

    public function refresh(string $resource, ?float $ttl = null): void;

    public function release(string $resource): void;
}
