<?php

namespace Celavi\Tool;

class Platform
{
    const OS_WINDOWS = 'windows';
    const OS_UNIX = 'unix';

    /**
     * Wether or not the script is running on a Windows platform.
     *
     * @return bool
     */
    public static function isWindows()
    {
        return self::OS_WINDOWS === self::getPlatform();
    }

    /**
     * Wether or not the script is running on a Unix platform.
     *
     * @return bool
     */
    public static function isUnix()
    {
        return self::OS_UNIX === self::getPlatform();
    }

    private static function getPlatform()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            return self::OS_WINDOWS;
        }

        return self::OS_UNIX;
    }
}
