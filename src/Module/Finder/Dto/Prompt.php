<?php

declare(strict_types=1);

namespace LLM\Assistant\Module\Finder\Dto;

final readonly class Prompt
{
    /**
     * @param int<0, max> $start
     * @param positive-int $length
     * @param non-empty-string $prompt
     */
    public function __construct(
        public readonly int $start,
        public readonly int $length,
        public readonly string $prompt,
    ) {}
}
