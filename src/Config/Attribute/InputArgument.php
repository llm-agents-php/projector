<?php

declare(strict_types=1);

namespace LLM\Assistant\Config\Attribute;

/**
 * @internal
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class InputArgument implements ConfigAttribute
{
    public function __construct(
        public string $name,
    ) {}
}
