<?php
namespace Tuum\Builder;

use Dotenv\Dotenv;

class Builder
{
    const APP_DIR = 'app-dir';
    const VAR_DIR = 'var-dir';
    const DEBUG = 'debug';
    const ENV_FILE = 'env_file';

    private $data;

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
            ];
    }

    /**
     * @param string $app_dir
     * @param string $var_dir
     * @param bool   $debug
     * @param array  $data
     * @return Builder
     */
    public static function forge($app_dir, $var_dir, $debug, $data = [])
    {
        $data = [
                self::APP_DIR => $app_dir,
                self::VAR_DIR => $var_dir,
                self::DEBUG   => $debug,
            ] + $data;

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
        /** @noinspection PhpIncludeInspection */
        $returned = include($__file);
        if (is_callable($returned)) {
            $returned = call_user_func($returned, $this);
        }

        return $returned;
    }

    /**
     * @param string $file
     * @return bool|mixed
     */
    public function load($file)
    {
        $file = $this->getAppDir() . DIRECTORY_SEPARATOR . $file;
        $returned = $this->execute($file);
        $this->data[$file] = $returned;
        
        return $returned;
    }

    /**
     *
     */
    public function loadEnv()
    {
        $env_dir = $this->get(self::ENV_FILE) ?: $this->getVarDir();
        $env     = new Dotenv($env_dir);
        $env->load();
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
     * @return bool
     */
    public function isDebug()
    {
        return (bool)$this->data[self::DEBUG];
    }

    /**
     * @return string
     */
    public function getAppDir()
    {
        return rtrim($this->data[self::APP_DIR], DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    public function getVarDir()
    {
        return rtrim($this->data[self::VAR_DIR], DIRECTORY_SEPARATOR);
    }
}