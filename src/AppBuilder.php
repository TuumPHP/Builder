<?php
namespace Tuum\Builder;

/**
 * Class AppBuilder
 *
 * a generic application builder for environment aware process.
 *
 * @package WScore\Site\Builder
 *          
 * @property Environment $env
 */
class AppBuilder
{
    use SettingTrait;
    
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
     * @var bool        debug or not
     */
    public $debug = false;

    /**
     * @var Environment
     */
    private $envObj;

    /**
     * @param string             $config_dir
     * @param string|null        $var_dir
     * @param null|Environment   $env
     */
    public function __construct($config_dir, $var_dir = null, $env = null)
    {
        $this->app_dir = $config_dir;
        $this->var_dir = $var_dir;
        $this->envObj  = $env ?: new Environment();
    }

    /**
     * @param string $key
     * @return null|mixed
     */
    public function __get($key)
    {
        if ($key === 'env') {
            return $this->envObj;
        }
        return null;
    }
    
    /**
     * forges AppBuilder.
     *
     * $options = array(
     *   'env'      => 'env-name',  // or array of env-names.
     *   'debug'    => true,      // or false.
     *   'env-file' => 'env-file-name',
     * )
     *
     * @param string      $config_dir
     * @param string|null $var_dir
     * @param array       $options
     * @return AppBuilder
     */
    public static function forge($config_dir, $var_dir = null, $options=[])
    {
        $builder = new self($config_dir, $var_dir);
        if (isset($options['env'])) {
            $builder->env->setEnvironment((array) $options['env']);
        }
        if (isset($options['debug'])) {
            $builder->debug = $options['debug'];
        }
        if (isset($options['env-file'])) {
            $builder->loadEnvironment($options['env-file']);
        }
        return $builder;
    }

    /**
     * @api
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
        foreach ($this->envObj->listEnvironments(['']) as $env) {
            $file = ($env ? $env . DIRECTORY_SEPARATOR : '') . $config;
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
        $list_env  = array_reverse($this->envObj->listEnvironments(['']));
        foreach ($list_env as $env) {
            $file = ($env ? $env . DIRECTORY_SEPARATOR : '') . $config;
            if ($this->execute($directory . $file) !== false) {
                return $this;
            }
        }

        return $this;
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
     * loads the environment from file at $this->var_dir.
     *
     * @api
     * @param string $env_file
     * @return $this
     */
    public function loadEnvironment($env_file)
    {
        if (is_null($this->var_dir)) {
            return $this;
        }
        $this->envObj->loadEnvironment(
            $this->var_dir . DIRECTORY_SEPARATOR . $env_file . '.php',
            [ 'builder' => $this, 'app' => $this->app]
        );

        return $this;
    }
}