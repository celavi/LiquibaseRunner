<?php

namespace Celavi\Command\Liquibase\Update;

use Celavi\Command\Liquibase\Common\Command;
use Celavi\Command\Liquibase\Common\CommandInterface;
use Celavi\Liquibase\Runner;
use Celavi\Exception\DomainException;
use Celavi\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class UpdateCommands extends Command implements CommandInterface
{
    const COMMAND_NAME = '';
    
    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<header>Database Update Commands</header>'."\r\n");
        try {
            $changeLogFile = $input->getOption('changeLogFile');
            if ($changeLogFile === null) {
                throw new RuntimeException('The "--changeLogFile" option is required.');
            }
            $output->writeln('<info>'.$this->getDescription().'</info>'."\r\n");
            $showCommand = $input->getOption('show-command');
            $app = $this->getSilexApplication();
            $projectPath = $this->getProjectDirectory();
            $runner = new Runner($app['config']['liquibase'], $projectPath);
            $runner->runUpdateCommands(static::COMMAND_NAME, $changeLogFile);
            if ($showCommand) {
                $output->writeln('<info>Command:</info>'."\r\n");
                $output->writeln($runner->getRunCommand());
            }
            $output->writeln('<ok>Ok:</ok>');
            $output->writeln('<info>' . $runner->getOutput() . '</info>');
        } catch (DomainException $ex) {
            $output->writeln('<error>Error:</error>');
            $output->writeln('<warning>'.$ex->getMessage().'</warning>');
        } catch (RuntimeException $ex) {
            $output->writeln('<runtime-error>'.$ex->getMessage().'</runtime-error>'."\r\n\r\n");
            $output->writeln('<info>'.$this->getHelp().'</info>');
        }
    }
    
    public function getCommandDefinition()
    {
        return array(
            new InputOption('changeLogFile', '', InputOption::VALUE_REQUIRED, 'The changelog file to use.'),
            new InputOption('show-command', 's', InputOption::VALUE_NONE, 'Displays the command which will run.'),
        );
    }
}
