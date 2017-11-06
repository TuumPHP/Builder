<?php
namespace Tuum\Builder;

use Dotenv\Dotenv;

class Builder implements \ArrayAccess
{
    use DataTrait;
    
    const APP_DIR     = 'app-dir';
    const VAR_DIR     = 'var-dir';
    const ENV_DIR     = 'env-dir';
    const DEBUG       = 'debug';

    const ENV_KEY               = 'env-key';
    const PROD_KEY              = 'prod-key';
    const ALLOW_DOTENV_OVERLOAD = 'allow-dotenv-overload';

    /**
     * @var mixed
     */
    private $app;

    /**
     * Builder constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setData(array_merge([
                self::APP_DIR => __DIR__,
                self::VAR_DIR => __DIR__,
                self::DEBUG   => false,
                self::ENV_KEY => 'APP_ENV',
                self::PROD_KEY => 'prod',
            ], $data));
    }

    /**
     * @param string $app_dir
     * @param string $var_dir
     * @param bool   $debug
     * @param array  $data
     * @return Builder
     */
    public static function forge($app_dir, $var_dir, $debug = true, $data = [])
    {
        $data = array_merge([
                self::APP_DIR => $app_dir,
                self::VAR_DIR => $var_dir,
                self::DEBUG   => $debug,
            ], $data);

        return new self($data);
    }

    /**
     * @param string $__file
     * @return bool|mixed
     */
    public function execute($__file)
    {
        if (substr($__file, -4) !== '.php') {
            $__file = $__file . '.php';
        }
        if (!file_exists($__file)) {
            return false;
        }

        /** @noinspection PhpUnusedLocalVariableInspection */
        $builder = $this;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $app     = $this->getApp();
        
        /** @noinspection PhpIncludeInspection */
        return include($__file);
    }

    /**
     * @param string $filename
     * @return bool|mixed
     */
    public function load($filename)
    {
        $file = $this->getAppDir() . DIRECTORY_SEPARATOR . $filename;
        $returned = $this->execute($file);
        if (!$this->has($filename)) {
            $this->set($filename, $returned);
        }
        
        return $returned;
    }

    /**
     * @param string $env_name
     * @return bool
     */
    public function loadEnv($env_name = '.env')
    {
        $env_file = $this->getVarDir() . '/' . $env_name;
        if (file_exists($env_file)) {
            $env     = new Dotenv($this->getVarDir(), $env_name);
            if ($this->get(self::ALLOW_DOTENV_OVERLOAD)) {
                $env->overload();
            }
            $env->load();
            return true;
        }
        return false;
    }

    /**
     * @param mixed $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * @return mixed|null
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return (bool)$this->get(self::DEBUG);
    }

    /**
     * @return string|null
     */
    public function getEnv()
    {
        $key = $this->get(self::ENV_KEY);
        return $this->get($key);
    }

    /**
     * @param string $environment
     * @return bool
     */
    public function isEnv($environment)
    {
        $env  = $this->getEnv();
        if ($env === $environment) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isEnvProd()
    {
        $env  = $this->getEnv();
        if (!$env) {
            return true;
        }
        $prod  = $this->get(self::PROD_KEY);
        return $env === $prod;
    }
    
    /**
     * @return string
     */
    public function getAppDir()
    {
        return rtrim($this->get(self::APP_DIR), DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    public function getVarDir()
    {
        return rtrim($this->get(self::VAR_DIR), DIRECTORY_SEPARATOR);
    }
}