<?php

declare(strict_types=1);

namespace LLM\Assistant\Config\Source;

use LLM\Assistant\Config\Attribute\XPath;

final class Directory
{
    #[XPath('./.')]
    public string $path;

    // todo not supported
    // #[XPath('@suffix')]
    // public string $suffix = '.php';
}
