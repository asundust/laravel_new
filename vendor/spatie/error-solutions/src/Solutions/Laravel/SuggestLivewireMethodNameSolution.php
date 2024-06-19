<?php

namespace Spatie\ErrorSolutions\Solutions\Laravel;

use Spatie\ErrorSolutions\Contracts\Solution;

class SuggestLivewireMethodNameSolution implements Solution
{
    public function __construct(
        protected string $methodName,
        protected string $componentClass,
        protected string $suggested
    ) {
    }

    public function getSolutionTitle(): string
    {
        return "Possible typo `{$this->componentClass}::{$this->methodName}`";
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getSolutionDescription(): string
    {
        return "Did you mean `{$this->componentClass}::{$this->suggested}`?";
    }

    public function isRunnable(): bool
    {
        return false;
    }
}
