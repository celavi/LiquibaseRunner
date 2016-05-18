<?php

namespace Celavi\Liquibase;

use Celavi\Tool\Platform;
use Celavi\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Runner
{
    const LIQUIBASE = '/src/Celavi/Liquibase/vendor/liquibase-3.4.2.jar';
    const JDBC_MYSQL = '/src/Celavi/Liquibase/vendor/lib/mysql-connector-java-5.1.39-bin.jar';
    const SNAKE_YAML = '/src/Celavi/Liquibase/vendor/lib/snakeyaml-1.13.jar';

    /**
     * @var array
     */
    private $params = array();

    /**
     * @var strings
     */
    private $projectPath = '';

    public function __construct($params, $projectPath)
    {
        $this->params = $params;
        $this->projectPath = $projectPath;
        $this->checkJava();
    }

    /**
     *
     * @param string $changeLogFile
     */
    public function runUpdate($changeLogFile)
    {
        $this->checkChangeLog($changeLogFile);
        $command = $this->getBaseCommand();
        $command .= ' --changeLogFile=' . $this->projectPath . $changeLogFile;
        $command .= ' update';
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

    protected function getBaseCommand()
    {
        $params = $this->params;

        $command = 'java -classpath '. $this->projectPath.self::SNAKE_YAML.
            ' -jar ' . $this->projectPath.self::LIQUIBASE.
            ' --driver='. $this->getJdbcDriverName($params['driver']).
            ' --classpath='.$this->getJdbcDriverClassPath($params['driver']).
            ' --url="'.$this->getJdbcDsn($params).'"';

        if ($params['user'] != "") {
            $command .= ' --username='.$params['user'];
        }

        if ($params['password'] != "") {
            $command .= ' --password='.$params['password'];
        }

		return $command;
    }

    /**
     * @param string $driver
     *
     * @return string
     *
     * @throws \Celavi\Exception\RuntimeException
     */
    private function getJdbcDriverName($driver)
    {
        switch($driver) {
            case 'pdo_mysql':
            case 'mysql':
				return "com.mysql.jdbc.Driver";
            default:
				throw new RuntimeException("Database driver class name for '$driver' not supported!");
        }
    }

    /**
	 * @param string $driver
	 * @return string
	 * @throws Celavi\Exception\RuntimeException
	 */
	private function getJdbcDriverClassPath($driver)
    {
        switch($driver) {
            case 'pdo_mysql':
            case 'mysql':
				return $this->projectPath . self::JDBC_MYSQL;
            default:
				throw new RuntimeException("Classpath containing JDBC Driver '$driver' not found!");
        }
    }

    /**
	 * @param array $params
	 * @return string
	 * @throws Celavi\Exception\RuntimeException
	 */
	private function getJdbcDsn($params)
    {
        switch($params['driver']) {
            case 'pdo_mysql':
			case 'mysql':
				return $this->getMysqlJdbcDsn($params);
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
        }
        else {
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
}
