<?php

declare(strict_types=1);

namespace LLM\Assistant;

use LLM\Assistant\Module\Common\Architecture;
use LLM\Assistant\Module\Common\OperatingSystem;
use LLM\Assistant\Module\Common\Stability;
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

        // Register bindings
        $this->container->bind(ConfigLoader::class, $args);
        $this->container->bind(Architecture::class);
        $this->container->bind(OperatingSystem::class);
        $this->container->bind(Stability::class);

        return $this;
    }
}
