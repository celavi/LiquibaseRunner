<?php

namespace Celavi\Command\Liquibase\Update;

class UpdateSql extends UpdateCommands
{
    /**
     * @var string
     */
    const COMMAND_NAME = 'updateSQL';

    public function getCommandName()
    {
        return self::COMMAND_NAME;
    }
    
    public function getCommandDescription()
    {
        return 'Writes SQL to update database to current version to STDOUT..';
    }

    public function getCommandHelp()
    {
        return <<<EOT
The <info>{$this->getName()}</info> command writes SQL to update database to current version to STDOUT.

<info>{$this->getName()} [--show-command] --changeLogFile=<path and filename></info>

EOT;
    }
}
