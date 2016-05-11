<?php

namespace Celavi\Command\Liquibase;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{
    protected function configure()
    {
		$this
			->setName('update')
			->setDescription('Updates database to current version.')
			->setDefinition(array(
				new InputOption('changeLogFile', '', InputOption::VALUE_REQUIRED, 'The changelog file to use.'),
				new InputOption('verbose', 'v', InputOption::VALUE_NONE, 'Displays the whole command which will run.')
			))
			->setHelp(<<<EOT
The <info>{$this->getName()}</info> Updates database to current version.

<info>{$this->getName()} [--verbose] --changeLogFile=<path and filename></info>

EOT
			);
	}

    /**
	 * Executes the current command.
	 *
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $changeLogFile = $input->getOption('changeLogFile');
    }
}
