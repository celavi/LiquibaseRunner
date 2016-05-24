<?php

namespace Celavi\Command\Liquibase\Common;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Command extends \Ivoba\Silex\Command\Command
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->setDefinition($this->getCommandDefinition())
            ->setHelp($this->getCommandHelp());
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
