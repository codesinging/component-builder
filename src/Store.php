<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:22
 */

namespace CodeSinging\ComponentBuilder;

use CodeSinging\Support\Arr;

class Store
{
    /**
     * The stored property values.
     * @var array
     */
    protected static $values = [];

    /**
     * Store a property value.
     *
     * @param string|array $name
     * @param mixed|null   $value
     */
    public static function set($name, $value = null)
    {
        is_string($name) and $name = [$name => $value];
        foreach ($name as $key => $val) {
            Arr::set(self::$values, $key, $val);
        }
    }

    /**
     * Get a stored property value.
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        return Arr::get(self::$values, $name, $default);
    }

    /**
     * Get all the stored property values.
     * @return array
     */
    public static function all()
    {
        return self::$values;
    }

    /**
     * Clear all values.
     */
    public static function clear()
    {
        self::$values = [];
    }
}