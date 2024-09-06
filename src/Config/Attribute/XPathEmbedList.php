<?php

declare(strict_types=1);

namespace LLM\Assistant\Config\Attribute;

/**
 * @internal
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class XPathEmbedList implements ConfigAttribute
{
    /**
     * @param non-empty-string $path
     * @param class-string $class
     */
    public function __construct(
        public string $path,
        public string $class,
    ) {}
}
