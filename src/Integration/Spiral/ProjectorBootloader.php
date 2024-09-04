<?php

declare(strict_types=1);

namespace LLM\Projector\Integration\Spiral;

use LLM\Agents\Agent\SymfonyConsole\Integrations\Spiral\SymfonyConsoleBootloader;
use Spiral\Boot\Bootloader\Bootloader;

final class ProjectorBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            SymfonyConsoleBootloader::class,
        ];
    }
}
