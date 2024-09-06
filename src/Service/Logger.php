<?php

declare(strict_types=1);

namespace LLM\Assistant\Service;

use Psr\Log\LoggerInterface;

interface Logger extends LoggerInterface
{
    public function exception(\Throwable $e, ?string $header = null, bool $important = true): void;
}
