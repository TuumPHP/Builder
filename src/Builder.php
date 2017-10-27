<?php
namespace Tuum\Builder;

use Dotenv\Dotenv;

class Builder
{
    const APP_DIR     = 'app-dir';
    const VAR_DIR     = 'var-dir';
    const ENV_DIR     = 'env-dir';
    const DEBUG       = 'debug';

    const ENV_KEY     = 'env-key';
    const PRODUCTION  = 'prod';

    /**
     * @var array|int
     */
    private $data = [];

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
        $this->data = $data + [
                self::APP_DIR => __DIR__,
                self::VAR_DIR => __DIR__,
                self::DEBUG   => false,
                self::ENV_KEY => 'APP_ENV',
            ];
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
        if (!isset($this->data[$filename])) {
            $this->data[$filename] = $returned;
        }
        
        return $returned;
    }

    /**
     * @param string $env_name
     * @return bool
     */
    public function loadEnv($env_name = '.env')
    {
        $env_dir = $this->get(self::ENV_DIR) ?: $this->getVarDir();
        if (file_exists($env_dir . '/' . $env_name)) {
            $env     = new Dotenv($env_dir, $env_name);
            $env->load();
            return true;
        }
        return false;
    }

    /**
     * @param string     $id
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get($id, $default = null)
    {
        if ($found = getenv($id)) {
            return $found;
        }

        return array_key_exists($id, $this->data) ? $this->data[$id] : $default;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->data);
    }

    /**
     * @param string $id
     * @param mixed  $value
     */
    public function set($id, $value)
    {
        $this->data[$id] = $value;
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
     * @param string $environment
     * @return bool
     */
    public function isEnv($environment)
    {
        $key  = $this->get(self::ENV_KEY);
        $env  = $this->get($key);
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
        if (!$this->get(self::ENV_KEY)) {
            return true;
        }
        return $this->isEnv($this->get(self::PRODUCTION));
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

    /**
     * @return int
     */
    public function getAll()
    {
        return $this->data;
    }
}