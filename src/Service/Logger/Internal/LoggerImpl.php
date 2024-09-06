<?php

declare(strict_types=1);

namespace LLM\Assistant\Service\Logger\Internal;

use Psr\Log\LoggerTrait;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console color logger
 *
 * @internal
 */
final class LoggerImpl implements \LLM\Assistant\Service\Logger\Logger
{
    use LoggerTrait;

    private readonly int $verbosityLevel;
    private readonly bool $quiet;

    public function __construct(
        private readonly ?OutputInterface $output = null,
    ) {
        $this->verbosityLevel = match (true) {
            $output?->isDebug() => 3,
            $output?->isVeryVerbose() => 2,
            $output?->isVerbose() => 1,
            default => 0,
        };

        $this->quiet = $output?->isQuiet() ?? false;
    }

    public function exception(\Throwable $e, ?string $header = null, bool $important = true): void
    {
        $r = "----------------------\n";
        // Print bold yellow header if exists
        if ($header !== null) {
            $r .= "\033[1;33m" . $header . "\033[0m\n";
        }
        // Print exception message
        $r .= $e->getMessage() . "\n";
        // Print file and line using green color and italic font
        $r .= "In \033[3;32m" . $e->getFile() . ':' . $e->getLine() . "\033[0m\n";
        // Print stack trace using gray
        $r .= "Stack trace:\n";
        // Limit stacktrace to 5 lines
        $stack = \explode("\n", $e->getTraceAsString());
        $r .= "\033[90m" . \implode("\n", \array_slice($stack, 0, \min(5, \count($stack)))) . "\033[0m\n";
        $r .= "\n";
        $this->log($important ? LogLevel::Error : LogLevel::Info, $r);
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if ($this->quiet) {
            return;
        }

        $level = LogLevel::tryFrom($level) ?? LogLevel::Info;

        if ($level->verbosityLevel() > $this->verbosityLevel) {
            return;
        }

        $this->output?->write($level->color() . $message . "\033[0m\n");
    }
}
