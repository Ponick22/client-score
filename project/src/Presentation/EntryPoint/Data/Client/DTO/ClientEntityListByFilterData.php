<?php

namespace App\Presentation\EntryPoint\Data\Client\DTO;

use App\Application\Client\Connector\Query\ClientEntityList\DTO\ClientEntityListByFilterInterface;

class ClientEntityListByFilterData implements ClientEntityListByFilterInterface
{
    private ?int $offset = null;
    private ?int $limit  = null;

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }
}
