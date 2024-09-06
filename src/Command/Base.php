<?php

declare(strict_types=1);

namespace LLM\Assistant\Command;

use LLM\Assistant\Bootstrap;
use LLM\Assistant\Service\Container;
use LLM\Assistant\Service\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
abstract class Base extends Command
{
    protected Logger $logger;
    protected Container $container;

    public function configure(): void
    {
        parent::configure();
        $this->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Path to the configuration file');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $this->container = Bootstrap::init($input, $output)->withConfig(
            // xml: $this->getConfigFile($input),
            inputOptions: $input->getOptions(),
            inputArguments: $input->getArguments(),
            environment: \getenv(),
        )->finish();
        $this->logger = $this->container->get(Logger::class);

        return Command::SUCCESS;
    }

    /**
     * @return non-empty-string|null Path to the configuration file
     */
    private function getConfigFile(InputInterface $input): ?string
    {
        /** @var string|null $config */
        $config = $input->getOption('config');
        $isConfigured = $config !== null;
        $config ??= './dload.xml';

        if (\is_file($config)) {
            return $config;
        }

        $isConfigured and throw new \InvalidArgumentException(
            'Configuration file not found: ' . $config,
        );

        return null;
    }
}
