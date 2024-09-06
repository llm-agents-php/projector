<?php

declare(strict_types=1);

namespace LLM\Assistant;

/**
 * @internal
 */
class Info
{
    public const NAME = 'AI Assistant';

    public const LOGO_CLI_COLOR = '';

    public const ROOT_DIR = __DIR__ . '/..';

    private const VERSION = 'experimental';

    /**
     * Returns the version of the Trap.
     *
     * @return non-empty-string
     */
    public static function version(): string
    {
        /** @var non-empty-string|null $cache */
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $fileContent = \file_get_contents(self::ROOT_DIR . '/resources/version.json');

        if ($fileContent === false) {
            return $cache = self::VERSION;
        }

        /** @var mixed $version */
        $version = \json_decode($fileContent, true)['.'] ?? null;

        return $cache = \is_string($version) && $version !== ''
            ? $version
            : self::VERSION;
    }
}
