<?php

declare(strict_types=1);

namespace LLM\Assistant\Service\Logger;

/**
 * @internal
 */
enum LogLevel: string
{
    case Emergency = 'emergency';
    case Alert = 'alert';
    case Critical = 'critical';
    case Error = 'error';
    case Warning = 'warning';
    case Notice = 'notice';
    case Info = 'info';
    case Debug = 'debug';

    public function color(): string
    {
        return match ($this) {
            self::Emergency => "\033[1;31m",
            self::Alert => "\033[1;31m",
            self::Critical => "\033[1;31m",
            self::Error => "\033[31m",
            self::Warning => "\033[33m",
            self::Notice => "\033[32m",
            self::Info => "\033[0m",
            self::Debug => "\033[36m",
        };
    }

    /**
     * @return int<0, 3>
     */
    public function verbosityLevel(): int
    {
        return match ($this) {
            self::Alert, self::Emergency, self::Error, self::Critical => 0,
            self::Warning => 1,
            self::Info, self::Notice => 2,
            self::Debug => 3,
        };
    }
}
