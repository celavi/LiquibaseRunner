<?php

namespace Celavi\Liquibase;

use Celavi\Tool\Platform;
use Celavi\Exception\RuntimeException;
use Celavi\Exception\DomainException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Runner
{
    const LIQUIBASE = '/src/Celavi/Liquibase/vendor/liquibase-3.4.2.jar';
    const JDBC_MYSQL = '/src/Celavi/Liquibase/vendor/lib/mysql-connector-java-5.1.39-bin.jar';
    const JDBC_SQLITE = '/src/Celavi/Liquibase/vendor/lib/sqlite-jdbc-3.8.11.2.jar';
    const SNAKE_YAML = '/src/Celavi/Liquibase/vendor/lib/snakeyaml-1.13.jar';

    /**
     * @var array
     */
    private $params = array();

    /**
     * @var string
     */
    private $projectPath = '';
    
    /**
     * @var string
     */
    private $output = '';
        
    /**
     *
     * @var string 
     */
    private $runCommand = '';

    public function __construct($params, $projectPath)
    {
        $this->params = $params;
        $this->projectPath = $projectPath;
        $this->checkJava();
    }

    /**
     *
     * @param string $commandName
     * @param string $changeLogFile
     */
    public function runUpdateCommands($commandName, $changeLogFile)
    {
        $this->checkChangeLog($changeLogFile);
        $command = $this->getBaseCommand();
        $command .= ' --changeLogFile=' . $this->projectPath . $changeLogFile;
        $command .= " $commandName";
        $this->runCommand = $command;
        $this->run();
    }
    
    /**
     * Runs current command
     * 
     * @throws DomainException
     */
    private function run()
    {
        $process = new Process($this->runCommand);
        try {
            $process->mustRun();

            $this->output = $process->getOutput();
            if (strlen($this->output) == 0) {
                $this->output = $process->getErrorOutput();
            }
        } catch (ProcessFailedException $e) {
            throw new DomainException($process->getErrorOutput());
        }
    }

    /**
     * @param string $file
     * @throws \Celavi\Exception\RuntimeException
     */
    private function checkChangeLog($file)
    {
        $changeLogFile = $this->projectPath. $file;
        if (!file_exists($changeLogFile)) {
            throw new RuntimeException("$changeLogFile file is missing - aborting!");
        }
    }

    /**
     * Combines Base Liquibase command
     * 
     * @return string
     */
    protected function getBaseCommand()
    {
        $params = $this->params;

        $command = 'java -classpath '. $this->projectPath.self::SNAKE_YAML.
            ' -jar ' . $this->projectPath.self::LIQUIBASE.
            ' --driver='. $this->getJdbcDriverName($params['driver']).
            ' --classpath='.$this->getJdbcDriverClassPath($params['driver']).
            ' --url="'.$this->getJdbcDsn($params).'"';

        if (array_key_exists('user', $params) && $params['user'] != "") {
            $command .= ' --username='.$params['user'];
        }

        if (array_key_exists('password', $params) && $params['password'] != "") {
            $command .= ' --password='.$params['password'];
        }

        return $command;
    }

    /**
     * @param string $driver
     * @return string
     * @throws \Celavi\Exception\RuntimeException
     */
    private function getJdbcDriverName($driver)
    {
        switch ($driver) {
            case 'pdo_mysql':
            case 'mysql':
                return 'com.mysql.jdbc.Driver';
            case 'sqlite':
                return 'org.sqlite.JDBC';
            default:
                throw new RuntimeException("Database driver class name for '$driver' not supported!");
        }
    }

    /**
     * @param string $driver
     * @return string
     * @throws \Celavi\Exception\RuntimeException
     */
    private function getJdbcDriverClassPath($driver)
    {
        switch ($driver) {
            case 'pdo_mysql':
            case 'mysql':
                return $this->projectPath . self::JDBC_MYSQL;
            case 'sqlite':
                return $this->projectPath . self::JDBC_SQLITE;
            default:
                throw new RuntimeException("Classpath containing JDBC Driver '$driver' not found!");
        }
    }

    /**
     * @param array $params
     * @return string
     * @throws \Celavi\Exception\RuntimeException
     */
    private function getJdbcDsn($params)
    {
        switch ($params['driver']) {
            case 'pdo_mysql':
            case 'mysql':
                return $this->getMysqlJdbcDsn($params);
            case 'sqlite':
                return $this->getSqliteJdbcDsn($params);
            default:
                throw new RuntimeException("Database JDBC '" . $params['driver'] . "'not supported!");
        }
    }

    /**
     * @param array $params
     * @return string
     */
    private function getMysqlJdbcDsn($params)
    {
        $dsn = "jdbc:mysql://";
        if ($params['host'] != "") {
            $dsn .= $params['host'];
        } else {
            $dsn .= 'localhost';
        }
        if ($params['port'] != "") {
            $dsn .= ":".$params['port'];
        }
        $dsn .= "/".$params['dbname'];
        $dsn .='?createDatabaseIfNotExist=true';
        if ($params['charset'] == 'UTF8') {
            $dsn .= "&useUnicode=true&characterEncoding=UTF-8";
        }
        return $dsn;
    }
    
    /**
     * 
     * @param array $params
     * @return string
     */
    private function getSqliteJdbcDsn($params)
    {
        $dsn = "jdbc:sqlite:";
        $dsn .= $this->projectPath . $params['dbname'];
        return $dsn;
    }

    /**
     * Output from run command
     * 
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }
        
    /**
     * Return Run Command as string
     * 
     * @return string
     */
    public function getRunCommand()
    {
        return $this->runCommand;
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
        $process->run();
        $output = $process->getOutput();
        if (strlen($output) == 0) {
            throw new RuntimeException('Java is not installed - aborting!');
        }
    }
}
