<?php
namespace Tuum\Builder;

/**
 * Class AppBuilder
 *
 * a generic application builder for environment aware process.
 *
 * @package WScore\Site\Builder
 */
class AppBuilder
{
    /**
     * @var mixed       the application to configure
     */
    public $app = null;

    /**
     * @var string      main config dir (ex: project-root/app/)
     */
    public $app_dir;

    /**
     * @var string      var dir (no version control) (ex: project-root/vars)
     */
    public $var_dir;

    /**
     * @var array       list of environment
     */
    private $environments = [''];

    /**
     * @var bool        debug or not
     */
    public $debug = false;

    /**
     * @var array
     */
    private $settings = [];

    /**
     * @param string      $config_dir
     * @param string|null $var_dir
     */
    public function __construct($config_dir, $var_dir = null)
    {
        // default configuration.
        $this->app_dir = $config_dir;
        $this->var_dir = $var_dir ?: dirname($config_dir) . '/var';
    }

    /**
     * @param string      $config_dir
     * @param string|null $var_dir
     * @return AppBuilder
     */
    public static function forge($config_dir, $var_dir = null)
    {
        return new self($config_dir, $var_dir);
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function setup(callable $callable)
    {
        $callable($this);

        return $this;
    }

    /**
     * read multiple configuration files at $this->app_dir/$file.
     *
     * this reads multiple configuration files under $app_dir.
     * if $app_dir = config and $config = mail, the files are,
     *   - config/mail.php
     *   - config/{$environment}/mail.php
     * 
     * always read the main config file (i.e. without environment), 
     * then the environment specific configuration file. 
     *
     * @api
     * @param string $config
     * @return $this
     */
    public function configure($config)
    {
        $directory = $this->app_dir . DIRECTORY_SEPARATOR;
        foreach ($this->listEnvForConf() as $env) {
            $file = ($env ? $env . '/' : '') . $config;
            $this->execute($directory . $file);
        }

        return $this;
    }

    /**
     * read only one configuration file for specified environment
     * at $this->app_dir/$file. reads config for production if 
     * no env-specific conf files are found.
     *
     * if $app_dir = config and $config = mail, searches for,
     *   - config/mail.php
     *   - config/{$environment}/mail.php
     * 
     * and reads the first configuration file found. 
     * 
     * @param string $config
     * @return $this
     */
    public function execConfig($config)
    {
        $directory = $this->app_dir . DIRECTORY_SEPARATOR;
        $list_env  = array_reverse($this->listEnvForConf());
        foreach ($list_env as $env) {
            $file = ($env ? $env . '/' : '') . $config;
            if ($this->execute($directory . $file) !== false) {
                return $this;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    private function listEnvForConf()
    {
        $list = array_merge([''], $this->environments);
        $list = array_unique($list);
        return $list;
    }
    
    /**
     * evaluate PHP file ($__file.php) and returns the value.
     * the file path must be an absolute path. 
     *
     * @api
     * @param string $__file
     * @return mixed|bool
     */
    public function execute($__file)
    {
        $__file = $__file . '.php';
        if (!file_exists($__file)) {
            return false;
        }
        /** @noinspection PhpUnusedLocalVariableInspection */
        $app = $this->app;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $builder = $this;

        /** @noinspection PhpIncludeInspection */

        return include($__file);
    }

    /**
     * loads the environment based configuration.
     *
     * @api
     * @param string $env_file
     * @return $this
     */
    public function loadEnvironment($env_file)
    {
        $directory = $this->var_dir . DIRECTORY_SEPARATOR;
        $environments = $this->execute($directory.'/'.$env_file);
        if ($environments === 1 || $environments === null) {
            $this->environments = [''];
        } else {
            $this->environments = (array)$environments;
        }

        return $this;
    }

    /**
     * sets $value as $key in local container.
     *
     * @api
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->settings[$key] = $value;

        return $this;
    }

    /**
     * gets $key from the local container.
     *
     * @api
     * @param string     $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->settings) ? $this->settings[$key] : $default;
    }

    /**
     * @api
     * @param string $key
     * @return bool
     */    
    public function has($key)
    {
        return array_key_exists($key, $this->settings);
    }

    /**
     * @api
     * @param string $env
     * @return bool
     */
    public function isEnvironment($env)
    {
        return in_array($env, $this->environments);
    }

    /**
     * @api
     * @return bool
     */
    public function isProduction()
    {
        return $this->isEnvironment('');
    }

    /**
     * @param string|array $env
     */
    public function addEnvironment($env)
    {
        $this->environments = array_merge($this->environments, (array) $env);
    }
}