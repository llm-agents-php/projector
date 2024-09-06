<?php

declare(strict_types=1);

namespace LLM\Assistant\Config;

use LLM\Assistant\Config\Attribute\InputOption;
use LLM\Assistant\Config\Attribute\XPath;
use LLM\Assistant\Config\Attribute\XPathEmbedList;
use LLM\Assistant\Config\Source\Directory;
use LLM\Assistant\Config\Source\File;

/**
 * @internal
 */
final class Source
{
    /**
     * File or directory path.
     * Support glob patterns.
     */
    #[InputOption('path')]
    public ?string $path = null;

    #[XPath('/ai/@cacheDir')]
    public string $cacheDir = './runtime/.ai';

    /**
     * @var File[]
     */
    #[XPathEmbedList('/ai/source/include/file', File::class)]
    public array $includeFile = [];

    /**
     * @var Directory[]
     */
    #[XPathEmbedList('/ai/source/include/directory', Directory::class)]
    public array $includeDir = [];

    /**
     * @var File[]
     */
    #[XPathEmbedList('/ai/source/exclude/file', File::class)]
    public array $excludeFile = [];

    /**
     * @var Directory[]
     */
    #[XPathEmbedList('/ai/source/exclude/directory', Directory::class)]
    public array $excludeDir = [];
}
