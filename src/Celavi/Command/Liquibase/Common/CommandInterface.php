<?php

namespace Celavi\Command\Liquibase\Common;

interface CommandInterface
{
    /**
     * Returns command Name
     */
    public function getCommandName();
    
    /**
     * Returns command description
     */
    public function getCommandDescription();
    
    /**
     * Returns command help description
     */
    public function getCommandHelp();
    
    /**
     * Returns command definition
     */
    public function getCommandDefinition();
}
