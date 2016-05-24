<?php

namespace Celavi\Command\Liquibase\Update;

class Update extends UpdateCommands
{
    /**
     * @var string
     */
    const COMMAND_NAME = 'update';

    public function getCommandName()
    {
        return self::COMMAND_NAME;
    }
    
    public function getCommandDescription()
    {
        return 'Updates database to current version.';
    }

    public function getCommandHelp()
    {
        return <<<EOT
The <info>{$this->getName()}</info> command updates database to current version.

<info>{$this->getName()} [--show-command] --changeLogFile=<path and filename></info>

EOT;
    }
}
