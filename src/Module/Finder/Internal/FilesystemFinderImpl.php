<?php

declare(strict_types=1);

namespace LLM\Assistant\Module\Finder\Internal;

use LLM\Assistant\Config\Source;
use LLM\Assistant\Module\Finder\FilesystemFinder;

final class FilesystemFinderImpl implements FilesystemFinder
{
    /**
     * @param bool|null $sort If true, sort by newest first
     */
    public function __construct(
        private readonly Source $config,
        private ?\DateTimeInterface $after = null,
        private ?bool $sort = null,
    ) {}

    public function getIterator(): \Traversable
    {
        return $this->finder()->getIterator();
    }

    public function after(\DateTimeInterface $date): static
    {
        return new self($this->config, $date, $this->sort);
    }

    public function oldest(): static
    {
        return new self($this->config, $this->after, false);
    }

    public function files(): \Traversable
    {
        return $this->finder()->files();
    }

    public function directories(): \Traversable
    {
        return $this->finder()->directories();
    }

    private function directoryToPattern(Source\Directory $dir): string
    {
        return $dir->path;
    }

    private function finder(): \Symfony\Component\Finder\Finder
    {
        // todo make better flexible finder
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->ignoreUnreadableDirs();
        $finder->in(\array_map($this->directoryToPattern(...), $this->config->includeDir));
        $finder->exclude(\array_map($this->directoryToPattern(...), $this->config->excludeDir));
        $finder->path(\array_map(static fn(Source\File $file): string => $file->path, $this->config->includeFile));
        $finder->notPath(\array_map(static fn(Source\File $file): string => $file->path, $this->config->excludeFile));
        $finder->exclude(\array_map($this->directoryToPattern(...), $this->config->excludeDir));
        // $finder->exclude($this->config->cacheDir);

        $this->after === null or $finder->date('>= ' . $this->after->format('Y-m-d H:i:s'));
        // todo direction
        $this->sort === null or $finder->sortByModifiedTime();

        return $finder;
    }
}
