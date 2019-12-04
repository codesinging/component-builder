<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:22
 */

namespace CodeSinging\ComponentBuilder;

use CodeSinging\Helpers\Arr;

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
     * @param string $name
     * @param mixed  $value
     */
    public static function set(string $name, $value)
    {
        Arr::set(self::$values, $name, $value);
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
}