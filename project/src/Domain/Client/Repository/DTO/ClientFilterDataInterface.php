<?php

namespace App\Domain\Client\Repository\DTO;

interface ClientFilterDataInterface
{
    public function getOffset(): ?int;
    public function getLimit(): ?int;
}
