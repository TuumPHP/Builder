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
     * @param string           $app_dir
     * @param string|null      $var_dir
     * @param null|Environment $env
     */
    public function __construct($app_dir, $var_dir = null, $env = null)
    {
        $this->app_dir = $app_dir;
        $this->var_dir = $var_dir;
        $this->envObj  = $env ?: new Environment();
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
     * @param string      $app_dir
     * @param string|null $var_dir
     * @param array       $options
     * @return AppBuilder
     */
    public static function forge($app_dir, $var_dir = null, $options=[])
    {
        $builder = new self($app_dir, $var_dir);
        if (isset($options['debug'])) {
            $builder->debug = $options['debug'];
        }
        // set environment
        if (isset($options['env'])) {
            // directly specify using 'env'.
            $builder->envObj->setEnvironment((array) $options['env']);
        } elseif (isset($options['env-file'])) {
            // or, set from 'env-file'.
            $builder->loadEnvironment($options['env-file']);
        }
        $options['app-dir'] = $app_dir;
        $options['var-dir'] = $var_dir;
        $builder->set('options', $options);
        return $builder;
    }

    /**
     * read the configuration script at $this->app_dir/{$env/}$file.
     *
     * if no environment scripts are found, read the production
     * (i.e. no $env/) script.
     *
     * if an environment, $env, is defined, read the scripts for the
     * environment, and terminate the loop. if no env-specific
     * scripts are found, reads the production script.
     *
     * if the env-specific script depends on the production script,
     * read the production script inside env-specific script, as
     * $builder->execute(__DIR__ . '/../your-scripts.php');
     *
     * @api
     * @param string $config
     * @return $this|mixed
     */
    public function configure($config)
    {
        $directory = $this->app_dir . DIRECTORY_SEPARATOR;
        $list_env  = array_reverse($this->envObj->listEnvironments(['']));
        foreach ($list_env as $env) {
            $file = $directory . ($env ? $env . DIRECTORY_SEPARATOR : '') . $config . '.php';
            if (file_exists($file)) {
                return $this->execute($file);
            }
        }

        return $this;
    }

    /**
     * evaluate PHP file ($__file.php) and returns the value.
     * the file path must be an absolute path.
     *
     * if a callable is returned from the script, builder
     * will execute the callable with $this as an argument.
     *
     * @api
     * @param string $__file
     * @return mixed|bool
     */
    public function execute($__file)
    {
        if (substr($__file, -4) !== '.php' ) {
            $__file = $__file . '.php';
        }
        if (!file_exists($__file)) {
            return false;
        }
        /** @noinspection PhpUnusedLocalVariableInspection */
        $app = $this->app;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $builder = $this;

        /** @noinspection PhpIncludeInspection */

        $returned = include($__file);
        if (is_callable($returned)) {
            $returned = call_user_func($returned, $this);
        }
        return $returned;
    }

    /**
     * loads the environment from file at $this->var_dir.
     * if $env_file is an array, use it as environment.
     *
     * @api
     * @param string|array $env_file
     * @return $this
     */
    public function loadEnvironment($env_file)
    {
        if (is_array($env_file)) {
            $this->envObj->setEnvironment($env_file);
            return $this;
        }
        if (is_null($this->var_dir)) {
            return $this;
        }
        $this->envObj->loadEnvironment(
            $this->var_dir . DIRECTORY_SEPARATOR . $env_file . '.php',
            [ 'builder' => $this, 'app' => $this->app]
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function isProduction()
    {
        return $this->envObj->isProduction();
    }

    /**
     * @param string $env
     * @return bool
     */
    public function isEnv($env)
    {
        return $this->envObj->is($env);
    }
}