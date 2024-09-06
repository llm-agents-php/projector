<?php

declare(strict_types=1);

namespace LLM\Assistant\Module\Finder\Internal;

use LLM\Assistant\Config\Source;
use LLM\Assistant\Module\Finder\Finder;

final class FinderImpl implements Finder
{
    public function __construct(
        private readonly Source $config,
        private ?\DateTimeInterface $after = null,
    ) {}

    public function getIterator(): \Traversable
    {
        return $this->finder()->getIterator();
    }

    public function after(\DateTimeInterface $date): static
    {
        return new self($this->config, $date);
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

        if ($this->after !== null) {
            $finder->date('> ' . $this->after->format('Y-m-d H:i:s'));
        }

        return $finder;
    }
}
