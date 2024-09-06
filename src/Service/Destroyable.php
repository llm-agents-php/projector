<?php

declare(strict_types=1);

namespace LLM\Assistant\Service;

/**
 * Should be used to destroy objects and free resources.
 *
 * @internal
 */
interface Destroyable
{
    public function destroy(): void;
}
