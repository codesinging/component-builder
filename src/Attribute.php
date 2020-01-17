<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2020/1/8 11:13
 */

namespace CodeSinging\ComponentBuilder;

use CodeSinging\Support\Str;

class Attribute implements Buildable
{
    /**
     * All of the attributes.
     * @var array
     */
    protected $items = [];

    /**
     * The original attribute data.
     * @var array
     */
    protected $data = [];

    /**
     * Attribute constructor.
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
        return array_key_exists($name, $this->data) || array_key_exists($this->fill($name, ':'), $this->data);
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
        return $this->data[$name] ?? ($this->data[$this->fill($name, ':')] ?? $default);
    }

    /**
     * Set attribute values.
     *
     * @param string|array     $name
     * @param array|mixed|null $value
     * @param null|mixed       $store
     *
     * @return $this
     */
    public function set($name, $value = null, $store = null)
    {
        if (is_string($name)) {
            $this->add($name, $value, $store);
        } elseif (is_array($name)) {
            $stores = is_array($value) ? $value : [];
            foreach ($name as $key => $value) {
                is_int($key) and list($key, $value) = [$value, null];
                $this->add($key, $value, $stores[$key] ?? null);
            }
        }

        return $this;
    }

    /**
     * Add an attribute item.
     *
     * @param string $name
     * @param null   $value
     * @param null   $store
     */
    public function add(string $name, $value = null, $store = null)
    {
        if (is_string($value)) {
            if (Str::startsWith($value, ':')) {
                $name = $this->fill($name, ':');
                $value = substr($value, 1);
            } elseif (Str::startsWith($value, ['\:', '\\'])) {
                $value = substr($value, 1);
            }
            $this->data[$name] = $value;
            if (!is_null($store)) {
                $name = $this->fill($name, ':');
                $this->store($value, $store);
            }
        } else {
            $this->data[$name] = $value;
            if (true === $value) {
                $name = $this->fill($name, ':');
                $value = 'true';
            } elseif (false === $value) {
                $name = $this->fill($name, ':');
                $value = 'false';
            } elseif (is_int($value) || is_float($value) || is_double($value)) {
                $name = $this->fill($name, ':');
            } elseif (is_array($value)) {
                $store = $value;
                $value = 'attributeStores.' . $name;
                $name = $this->fill($name, ':');
                $this->store($value, $store);
            }
        }

        $this->items[$name] = $value;
    }

    /**
     * Get the filled name with a prefix.
     *
     * @param string $name
     * @param string $prefix
     *
     * @return string
     */
    public function fill(string $name, string $prefix = '')
    {
        return $prefix ? Str::start($name, $prefix) : $name;
    }

    /**
     * Store attribute to stores.
     *
     * @param array|string $name
     * @param null|mixed   $value
     */
    public function store($name, $value = null)
    {
        Store::set($name, $value);
    }

    /**
     * Determine if the attribute is empty.
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Get all of the attributes.
     * @return array
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * Get all original data.
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Create attribute string.
     *
     * @param string $name
     * @param null   $value
     *
     * @return string
     */
    public function create(string $name, $value = null)
    {
        if (is_null($value)) {
            return sprintf('%s', $name);
        }

        return sprintf('%s="%s"', $name, $value);
    }

    /**
     * Build the attributes to string.
     * @return string
     */
    public function __toString()
    {
        $arr = [];
        foreach ($this->items as $name => $value) {
            $arr[] = $this->create($name, $value);
        }

        return implode(' ', $arr);
    }
}