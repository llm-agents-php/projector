<?php

declare(strict_types=1);

namespace LLM\Assistant\Module\Finder\Internal;

use LLM\Assistant\Config\Source;
use LLM\Assistant\Module\Finder\Finder;

final class FinderImpl implements Finder
{
    public function __construct(
        Source $config,
        private \Symfony\Component\Finder\Finder $finder,
    ) {
        $finder->ignoreUnreadableDirs();
        $finder->in(\array_map($this->directoryToPattern(...), $config->includeDir));
        $finder->exclude(\array_map($this->directoryToPattern(...), $config->excludeDir));
        $finder->path(\array_map(static fn(Source\File $file): string => $file->path, $config->includeFile));
        $finder->notPath(\array_map(static fn(Source\File $file): string => $file->path, $config->excludeFile));

        // $config->path === null ?: $finder->path($config->path);

        // todo cached last scan
        // $this->finder->date()
    }

    public function getIterator(): \Traversable
    {
        return $this->finder->getIterator();
    }

    public function files(): \Traversable
    {
        foreach ($this->getIterator() as $name => $file) {
            if ($file->isFile()) {
                yield $name => $file;
            }
        }
    }

    public function directories(): \Traversable
    {
        foreach ($this->getIterator() as $name => $file) {
            if ($file->isDir()) {
                yield $name => $file;
            }
        }
    }

    private function directoryToPattern(Source\Directory $dir): string
    {
        return $dir->path;
    }
}
