<?php

namespace Celavi\Command\Liquibase;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Celavi\Liquibase\Runner;
use Celavi\Exception\RuntimeException;

class Update extends Command
{
    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Updates database to current version.')
            ->setDefinition(array(
                new InputOption('changeLogFile', '', InputOption::VALUE_REQUIRED, 'The changelog file to use.'),
                new InputOption('show-command', 's', InputOption::VALUE_NONE, 'Displays the whole command which will run.'),
            ))
            ->setHelp(<<<EOT
The <info>{$this->getName()}</info> command updates database to current version.

<info>{$this->getName()} [--show-command] --changeLogFile=<path and filename></info>

EOT
            );
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<header>Liquibase Runner</header>'."\r\n");
        try {
            $changeLogFile = $input->getOption('changeLogFile');
            if ($changeLogFile === null) {
                throw new RuntimeException('The "--changeLogFile" option is required.');
            }
            $output->writeln('<info>'.$this->getDescription().'</info>'."\r\n");
            $showCommand = $input->getOption('show-command');
            $app = $this->getApplication();
            $runner = new Runner();
            $runner->runUpdate();
            $output = $runner->getOutput();
        } catch (\RuntimeException $ex) {
            $output->writeln('<runtime-error>'.$ex->getMessage().'</runtime-error>'."\r\n\r\n");
            $output->writeln('<info>'.$this->getHelp().'</info>');
        }
    }

    /**
     * Override run command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        // Set extra colors.
        $formatter = new OutputFormatter($output->isDecorated());
        $formatter->setStyle('header', new OutputFormatterStyle('blue', 'black', array('bold')));
        $formatter->setStyle('runtime-error', new OutputFormatterStyle('white', 'red', array('bold')));
        $formatter->setStyle('error', new OutputFormatterStyle('red', 'black', array('bold')));
        $formatter->setStyle('warning', new OutputFormatterStyle('white', 'yellow', array('bold')));
        $formatter->setStyle('ok', new OutputFormatterStyle('green', 'black', array('bold')));
        $formatter->setStyle('info', new OutputFormatterStyle('yellow', 'black', array('bold')));
        $output->setFormatter($formatter);

        return parent::run($input, $output);
    }
}
