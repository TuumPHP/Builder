<?php
namespace Tuum\Builder;

trait OptionsTrait
{
    /**
     * @var array
     */
    private $options = [];

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
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @param array $option
     */
    protected function setOptions(array $option)
    {
        $this->options = $option;
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
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }

    /**
     * @api
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->options);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}