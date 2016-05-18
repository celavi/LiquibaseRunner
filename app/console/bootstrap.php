<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Silex\Application;
use Celavi\Silex\Provider\YamlConfigServiceProvider;
use Celavi\Exception\RuntimeException;

try {
    // Silex Application wraps Pimple Container
    $app = new Application();
    // Parse Yaml Config File
    $app->register(new YamlConfigServiceProvider(__DIR__.'/../config/consoleConfig.yml'));

    return $app;
} catch (RuntimeException $ex) {
    die('Runtime Error, stopping application. Reason: '.$ex->getMessage());
}
