<?php

declare(strict_types=1);

namespace LLM\Assistant\Command;

use LLM\Assistant\Module\Finder\Finder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
#[AsCommand(
    name: 'run',
    description: 'Run the Assistant',
)]
final class Run extends Base
{
    public function configure(): void
    {
        parent::configure();
        $this->addOption('path', null, InputOption::VALUE_OPTIONAL, 'Path to file or directory', ".");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $this->logger->alert('Assistant is running');

        $finder = $this->container->get(Finder::class);

        foreach ($finder->files() as $name => $file) {
            $this->logger->info($name);
        }

        return Command::SUCCESS;
    }
}
