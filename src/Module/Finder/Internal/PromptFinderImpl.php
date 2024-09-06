<?php

declare(strict_types=1);

namespace LLM\Assistant\Module\Finder\Internal;

use LLM\Assistant\Module\Finder\Dto\File;
use LLM\Assistant\Module\Finder\Dto\Prompt;
use LLM\Assistant\Module\Finder\FilesystemFinder;
use LLM\Assistant\Module\Finder\PromptFinder;
use LLM\Assistant\Service\Cache;
use LLM\Assistant\Service\Logger;

final class PromptFinderImpl implements PromptFinder
{
    private const CACHE_KEY = 'prompt_finder';

    private ?PromptCache $lastCache = null;

    public function __construct(
        private readonly FilesystemFinder $files,
        private readonly Cache $cache,
        private readonly Logger $logger,
    ) {}

    public function getNext(): ?File
    {
        $cache = $this->getOrUpdateCache();

        // Get last modified files
        $files = $cache->lastScan === null
            ? $this->files->oldest()->files()
            : $this->files->after($cache->lastScan)->oldest()->files();

        foreach ($files as $file) {
            $result = $this->scanFile($file);
            if ($result !== null) {
                // Update modified time in cache DTO to be saved later
                $mtime = $file->getMTime();
                $mtime === false or $cache->lastScan = new \DateTimeImmutable('@' . $mtime);

                $this->lastCache = $cache;
                return $result;
            }
        }

        return null;
    }

    private function getOrUpdateCache(): PromptCache
    {
        try {
            if ($this->lastCache !== null) {
                $this->cache->set(self::CACHE_KEY, $this->lastCache);
                return $this->lastCache;
            }

            return $this->cache->get(self::CACHE_KEY) ?? new PromptCache();
        } catch (\Throwable) {
            return new PromptCache();
        }
    }

    private function scanFile(\SplFileInfo $file): ?File
    {
        $content = \file_get_contents($file->getPathname());
        $this->logger->debug(
            \sprintf(
                'Scanning MT: %s Path: %s',
                (new \DateTimeImmutable('@' . (int) $file->getMTime()))->format('Y-m-d H:i:s'),
                $file->getPathname(),
            ),
        );

        // Find all inline prompts started with "#AI" from a new line
        $matches = [];
        $result = \preg_match_all(
            '/^[ \\t]*#AI[ \\t]+(.+)$/m',
            $content,
            $matches,
            \PREG_OFFSET_CAPTURE,
        );

        if ($result === 0) {
            return null;
        }

        $prompts = [];
        /**
         * @var array{
         *     list<array{non-empty-string, int<0, max>}>,
         *     list<array{non-empty-string, positive-int}>
         * } $matches
         */
        foreach ($matches[0] as $key => $match) {
            $prompts[] = new Prompt($match[1], \strlen($match[0]), $matches[1][$key][0]);
        }

        return new File($file, $content, $prompts);
    }
}
