<?php
namespace Tuum\Builder;

trait SettingTrait
{
    /**
     * @var array
     */
    private $settings = [];

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
}