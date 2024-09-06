<?php

declare(strict_types=1);

namespace LLM\Assistant\Module\Finder\Dto;

final readonly class File
{
    /**
     * @param list<Prompt> $prompts
     */
    public function __construct(
        public readonly \SplFileInfo $file,
        public readonly string $content,
        public readonly array $prompts,
    ) {}
}
