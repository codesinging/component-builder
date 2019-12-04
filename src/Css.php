<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:16
 */

namespace CodeSinging\ComponentBuilder;

use Closure;

class Css extends Builder
{
    /**
     * All of the css class items.
     * @var array
     */
    protected $items = [];

    /**
     * Css constructor.
     *
     * @param string|array|Css|Closure $classes
     * @param string|array|Css|Closure $mergeCss
     */
    public function __construct($classes = '', $mergeCss = '')
    {
        $this->add($classes)->add($mergeCss);
    }

    /**
     * Reset the css classes data.
     *
     * @param string|array|Css|Closure $classes
     * @param string|array|Css|Closure $mergeCss
     *
     * @return $this
     */
    public function reset($classes = '', $mergeCss = '')
    {
        $this->items = [];
        $this->add($classes)->add($mergeCss);
        return $this;
    }

    /**
     * Parse the specified css to an array.
     *
     * @param string|array|Css|Closure $classes
     *
     * @return array
     */
    protected function parse($classes)
    {
        if ($classes instanceof Closure) {
            $self = new self();
            $classes = call_user_func($classes, $self) ?? $self;
        }
        if (empty($classes)) {
            return [];
        }
        if (is_string($classes)) {
            $classes = preg_split("/[\s,]+/", $classes);
        } elseif ($classes instanceof self) {
            $classes = $classes->all();
        }
        return is_array($classes) ? $classes : [];
    }

    /**
     * Filter the existed classes.
     *
     * @param array $classes
     *
     * @return array
     */
    protected function filter(array $classes)
    {
        foreach ($classes as $index => $class) {
            if ($this->has($class)) {
                unset($classes[$index]);
            }
        }
        return $classes;
    }

    /**
     * Add or prepend classes data.
     *
     * @param string|array|Css|Closure $classes
     * @param bool                     $prepend
     *
     * @return $this
     */
    public function add($classes, bool $prepend = false)
    {
        $classes = $this->filter($this->parse($classes));
        if ($classes) {
            if ($prepend) {
                $this->items = array_merge($classes, $this->items);
            } else {
                $this->items = array_merge($this->items, $classes);
            }
        }
        return $this;
    }

    /**
     * Add classes onto the beginning.
     *
     * @param string|array|Css|Closure $classes
     *
     * @return $this
     */
    public function prepend($classes)
    {
        return $this->add($classes, true);
    }

    /**
     * Determine if the given class exists.
     *
     * @param string $class
     *
     * @return bool
     */
    public function has(string $class)
    {
        return in_array($class, $this->items);
    }

    /**
     * Determine if the classes is empty.
     * @return bool
     */
    public function empty()
    {
        return empty($this->items);
    }

    /**
     * Return all the classes.
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Build the class data to string.
     * @return string
     */
    public function build()
    {
        return implode(' ', $this->items);
    }
}