<?php
namespace Tuum\Builder;

trait DataTrait
{
    /**
     * @var array|int
     */
    private $data = [];

    protected function setData(array $data)
    {
        $this->data = $data;
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
     * @return int
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->has($offset)) {
            unset($this->data[$offset]);
        }
    }
}