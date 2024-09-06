<?php

declare(strict_types=1);

namespace LLM\Assistant;

use LLM\Assistant\Module\Common\Architecture;
use LLM\Assistant\Module\Common\OperatingSystem;
use LLM\Assistant\Module\Common\Stability;
use LLM\Assistant\Module\Finder\Finder;
use LLM\Assistant\Module\Finder\Internal\FinderImpl;
use LLM\Assistant\Service\Container;
use LLM\Assistant\Service\Container\ContainerImpl;
use LLM\Assistant\Service\Container\Injection\ConfigLoader;
use LLM\Assistant\Service\Logger;
use LLM\Assistant\Service\Logger\LoggerImpl;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Build the container based on the configuration.
 *
 * @internal
 */
final class Bootstrap
{
    private function __construct(
        private Container $container,
    ) {}

    public static function init(InputInterface $input, OutputInterface $output): self
    {
        $container = new ContainerImpl();

        $container->set($input, InputInterface::class);
        $container->set($output, OutputInterface::class);
        $container->set(new SymfonyStyle($input, $output), StyleInterface::class);
        $container->set(new LoggerImpl($output), Logger::class);

        return new self($container);
    }

    public function finish(): Container
    {
        $c = $this->container;
        unset($this->container);

        $c->bind(Finder::class, FinderImpl::class);

        return $c;
    }

    /**
     * @param non-empty-string|null $xml File or XML content
     */
    public function withConfig(
        ?string $xml = null,
        array $inputOptions = [],
        array $inputArguments = [],
        array $environment = [],
    ): self {
        $args = [
            'env' => $environment,
            'inputArguments' => $inputArguments,
            'inputOptions' => $inputOptions,
        ];

        // XML config file
        $xml === null or $args['xml'] = $this->readXml($xml);

        // Register bindings
        $this->container->bind(ConfigLoader::class, $args);

        return $this;
    }

    private function readXml(string $fileOrContent): string
    {
        // Load content
        if (\str_starts_with($fileOrContent, '<?xml')) {
            $xml = $fileOrContent;
        } else {
            \file_exists($fileOrContent) or throw new \InvalidArgumentException('Config file not found.');
            $xml = \file_get_contents($fileOrContent);
            $xml === false and throw new \RuntimeException('Failed to read config file.');
        }

        // Validate Schema
        // todo

        return $xml;
    }
}
