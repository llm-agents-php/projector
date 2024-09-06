<?php

declare(strict_types=1);

namespace LLM\Assistant\Module\Finder;

use LLM\Assistant\Module\Finder\Dto\File;

/**
 * Finds new prompts
 */
interface PromptFinder
{
    public function getNext(): ?File;
}
