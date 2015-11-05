<?php
namespace Tuum\Builder;

class Environment
{
    /**
     * list of environments
     *
     * @var array
     */
    private $environments = [''];

    /**
     * lists environments but production always at the first environment.
     * used primarily for internal use. 
     *
     * @param array $env
     * @return array
     */
    public function listEnvironments($env = [])
    {
        $list = array_merge($env, $this->environments);
        $list = array_unique($list);

        return $list;
    }

    /**
     * loads the environment based configuration.
     * used primarily for internal use.
     *
     * @param string $env_file
     * @param array  $__data
     * @return $this
     */
    public function loadEnvironment($env_file, $__data = [])
    {
        if (!file_exists($env_file)) {
            return false;
        }
        extract($__data);
        /** @noinspection PhpIncludeInspection */
        $environments = include($env_file);
        if ($environments !== 1 && $environments !== null) {
            $this->environments = (array)$environments;
        }
        return true;
    }

    /**
     * @api
     * @param string $env
     * @return bool
     */
    public function is($env)
    {
        return in_array($env, $this->environments);
    }

    /**
     * @api
     * @return bool
     */
    public function isProduction()
    {
        return $this->is('');
    }

    /**
     * @api
     * @param string|array $env
     */
    public function setEnvironment($env)
    {
        $this->environments = (array)$env;
    }

}