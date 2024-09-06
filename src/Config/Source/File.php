<?php

declare(strict_types=1);

namespace LLM\Assistant\Config\Source;

use LLM\Assistant\Service\Container\Attribute\XPath;

final class File
{
    // XPath value
    #[XPath('./.')]
    public string $path;
}
