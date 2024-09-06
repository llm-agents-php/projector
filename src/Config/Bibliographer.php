<?php

declare(strict_types=1);

namespace LLM\Assistant\Config;

use LLM\Assistant\Config\Attribute\Env;

/**
 * @internal
 */
final class Bibliographer
{
    #[Env('BIBLIOGRAPHER_TOKEN')]
    public ?string $token = null;
}
