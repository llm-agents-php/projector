<?php

declare(strict_types=1);

namespace LLM\Assistant\Module\Finder;

/**
 * Finds files
 *
 * @extends \IteratorAggregate<string, \SplFileInfo>
 */
interface Finder extends \IteratorAggregate
{
    /**
     * @return \Traversable<string, \SplFileInfo>
     */
    public function getIterator(): \Traversable;

    /**
     * @return \Traversable<string, \SplFileInfo>
     */
    public function files(): \Traversable;

    /**
     * @return \Traversable<string, \SplFileInfo>
     */
    public function directories(): \Traversable;
}
