<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:06
 */

namespace CodeSinging\ComponentBuilder;

use CodeSinging\Helpers\Str;

class Property extends Builder
{
    /**
     * All of the attributes.
     * @var array
     */
    protected $items = [];

    /**
     * Property constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = null)
    {
        $this->set($attributes);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name)
    {
        return array_key_exists($name, $this->items);
    }

    /**
     * Get the specified attribute value.
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        return $this->items[$name] ?? $default;
    }

    /**
     * Set attribute values.
     *
     * @param string|array      $name
     * @param mixed|array|null  $value
     * @param string|array|null $bind
     * @param bool              $store
     *
     * @return $this
     */
    public function set($name, $value = null, $bind = null, bool $store = false)
    {
        if ($name) {
            $props = [];
            $binds = [];
            $values = [];
            if (is_string($name)) {
                $props = [$name => $value];
                if ($bind) {
                    $binds = [$name => $bind];
                    $store and $values = [$name];
                }
            } elseif (is_array($name)) {
                $props = $name;
                is_array($value) and $binds = $value;
                is_array($bind) and $values = $bind;
            }
            foreach ($props as $name => $value) {
                is_int($name) and list($name, $value) = [$value, null];

                if (isset($binds[$name])) {
                    $bind = $binds[$name];
                    $this->items[$this->parseName($name, ':')] = $bind;
                    in_array($name, $values) and $this->store($bind, $value);
                } else {
                    $this->items[$name] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Determine if the attribute is empty.
     * @return bool
     */
    public function empty()
    {
        return empty($this->items);
    }

    /**
     * Get all of the attributes.
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Create attribute string.
     *
     * @param string      $key
     * @param string|array|bool|null $value
     *
     * @return string
     */
    public function create(string $key,  $value = null)
    {
        if (is_null($value)) {
            return sprintf('%s', $key);
        }

        if ($value===true){
            $value = 'true';
            $key = Str::start($key, ':');
        } elseif ($value===false){
            $value = 'false';
            $key = Str::start($key, ':');
        } elseif (is_array($value)){
            $value = addslashes(json_encode($value));
            $key = Str::start($key, ':');
        }

        return sprintf('%s="%s"', $key, $value);
    }

    /**
     * Parse property name.
     *
     * @param string $name
     * @param string $prefix
     *
     * @return string
     */
    protected function parseName(string $name, string $prefix = '')
    {
        if ($prefix) {
            $name = Str::start($name, $prefix);
        }
        return $name;
    }

    /**
     * Store value to Property store.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    protected function store(string $name, $value)
    {
        Store::set($name, $value);
        return $this;
    }

    /**
     * Build the attribute to string.
     * @return string|void
     */
    public function build()
    {
        $arr = [];
        foreach ($this->items as $key => $value) {
            $arr[] = $this->create($key, $value);
        }
        return $arr ? ' ' . implode(' ', $arr) : '';
    }
}