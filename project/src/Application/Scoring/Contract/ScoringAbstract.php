<?php

namespace App\Application\Scoring\Contract;

use App\Application\Scoring\DTO\ScoringData;

abstract readonly class ScoringAbstract implements ScoringInterface
{
    public function __construct(
        protected array $scoringRules,
    ) {}

    abstract public function getName(): string;

    public function scoring(mixed $value): ScoringData
    {
        $score = $this->getRules()[$value] ?? $this->getDefaultScore();

        return new ScoringData(
            $score,
            $value,
            $this->getName()
        );
    }

    protected function getRules(): array
    {
        return $this->scoringRules[$this->getName()]['list'] ?? [];
    }

    protected function getDefaultScore(): int
    {
        return $this->scoringRules[$this->getName()]['default'] ?? 0;
    }
}
