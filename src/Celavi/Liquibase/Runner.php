<?php

namespace Celavi\Liquibase;

use Celavi\Tool\Platform;
use Celavi\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Runner
{
    const LIQUIBASE = '/vendor/liquibase-3.4.2.jar';
    const JDBC_MYSQL = '/vendor/jdbc/mysql-connector-java-5.1.38-bin.jar';

    public function __construct()
    {
        $this->checkJava();
        $this->checkLiquibase();
    }

    public function runUpdate()
    {

    }

    public function getOutput()
    {

    }

    /**
     * Checks if we have Java installed.
     *
     * @throws RuntimeException
     */
    private function checkJava()
    {
        if (Platform::isWindows()) {
            $command = 'java -version > "C:\temp\null" 2>&1 && echo "Java is here"';
        } else {
            $command = 'java -version > /dev/null 2>&1 && echo "Java is here"';
        }

        $process = new Process($command);
        try {
            $process->mustRun();
            $output = $process->getOutput();
            if (strlen($output) == 0) {
                throw new RuntimeException('Java is not installed - aborting!');
            }
        } catch (ProcessFailedException $ex) {
            throw new RuntimeException($ex->getMessage());
        }
    }

    /**
     * Checks if we have Liquibase jar file in place.
     *
     * @throws RuntimeException
     */
    private function checkLiquibase()
    {
        $jarFile = __DIR__.self::LIQUIBASE;
        if (!file_exists($jarFile)) {
            throw new RuntimeException(self::LIQUIBASE.' file is missing - aborting!');
        }
    }
}
