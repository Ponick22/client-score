<?php

namespace App\Application\Scoring\DTO;

readonly class ScoringData
{
    public function __construct(
        protected int    $score,
        protected string $value,
        protected string $name,
    ) {}

    public function getScore(): int
    {
        return $this->score;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
