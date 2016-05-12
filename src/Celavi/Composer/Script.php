<?php

namespace Celavi\Composer;

class Script
{
    public static function install()
    {
        chmod('bin/liquibase', 0500);
    }
}
