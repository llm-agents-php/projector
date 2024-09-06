<?php

declare(strict_types=1);

namespace LLM\Assistant\Module\Finder;

/**
 * Finds files
 *
 * @extends \IteratorAggregate<string, \SplFileInfo>
 */
interface FilesystemFinder extends \IteratorAggregate
{
    /**
     * @return \Traversable<string, \SplFileInfo>
     */
    public function getIterator(): \Traversable;

    public function after(\DateTimeInterface $date): static;

    /**
     * Sort by modify time, oldest first
     */
    public function oldest(): static;

    /**
     * @return \Traversable<string, \SplFileInfo>
     */
    public function files(): \Traversable;

    /**
     * @return \Traversable<string, \SplFileInfo>
     */
    public function directories(): \Traversable;
}
