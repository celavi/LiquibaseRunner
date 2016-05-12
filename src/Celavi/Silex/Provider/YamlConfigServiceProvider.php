<?php

namespace Celavi\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;
use Celavi\Exception\RuntimeException;

class YamlConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $file;

    /**
     * Class Constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new RuntimeException($file . ' file is missing - aborting!');
        }
        $this->file = $file;
    }

    /**
     * Registers services on the given app.
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $config = Yaml::parse(file_get_contents($this->file));

        if (is_array($config)) {
            $this->importSearch($config, $app);

            if (isset($app['config']) && is_array($app['config'])) {
                $app['config'] = array_replace_recursive($app['config'], $config);
            } else {
                $app['config'] = $config;
            }
        }
    }

    /**
     * Bootstraps the application.
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }

    /**
     * Looks for import directives..
     *
     * @param array $config The result of Yaml::parse().
     */
    protected function importSearch(&$config, $app)
    {
        foreach ($config as $key => $value) {
            if ($key == 'imports') {
                foreach ($value as $resource) {
                    $base_dir = str_replace(basename($this->file), '', $this->file);
                    $new_config = new self($base_dir.$resource['resource']);
                    $new_config->register($app);
                }
                unset($config['imports']);
            }
        }
    }
}
