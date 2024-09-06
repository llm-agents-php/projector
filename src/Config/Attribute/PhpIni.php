<?php

declare(strict_types=1);

namespace LLM\Assistant\Config\Attribute;

/**
 * @internal
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final class PhpIni implements ConfigAttribute
{
    public function __construct(
        public string $option,
    ) {}
}
