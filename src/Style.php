<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:20
 */

namespace CodeSinging\ComponentBuilder;

use Closure;

class Style extends Builder
{
    /**
     * All of the style items.
     * @var array
     */
    protected $items = [];

    /**
     * Style constructor.
     *
     * @param string|array|Closure|Style $styles
     * @param string|array|Closure|Style $mergeStyles
     */
    public function __construct($styles = '', $mergeStyles = '')
    {
        $this->add($styles)->add($mergeStyles);
    }

    /**
     * Reset the styles data.
     *
     * @param string|array|Closure|Style $styles
     * @param string|array|Closure|Style $mergeStyles
     *
     * @return $this
     */
    public function reset($styles = '', $mergeStyles = '')
    {
        $this->items = [];
        $this->add($styles)->add($mergeStyles);
        return $this;
    }

    /**
     * Add or prepend style data.
     *
     * @param string|array|Closure|Style $styles
     * @param bool                       $prepend
     *
     * @return $this
     */
    public function add($styles, bool $prepend = false)
    {
        $styles = $this->parse($styles);
        if ($styles) {
            if ($prepend) {
                $this->items = array_merge($styles, $this->items);
            } else {
                $this->items = array_merge($this->items, $styles);
            }
        }
        return $this;
    }

    /**
     * Prepend style data.
     *
     * @param string|array|Closure|Style $styles
     *
     * @return $this
     */
    public function prepend($styles)
    {
        return $this->add($styles, true);
    }

    /**
     * Parse the styles to a string.
     *
     * @param string|array|Closure|Style $styles
     *
     * @return array
     */
    protected function parse($styles)
    {
        if ($styles instanceof Closure) {
            $self = new self();
            $styles = call_user_func($styles, $self) ?: $self;
        }
        if (empty($styles)) {
            return [];
        }
        if (is_string($styles)) {
            return $this->convert($styles);
        }
        if (is_array($styles)) {
            return $styles;
        }
        if ($styles instanceof self) {
            return $styles->all();
        }
        return [];
    }

    /**
     * Convert the styles string to an array.
     *
     * @param string $styles
     *
     * @return array
     */
    protected function convert(string $styles)
    {
        $result = [];
        $arr = explode(';', $styles);
        foreach ($arr as $item) {
            $item = trim($item);
            if ($item) {
                list($key, $value) = explode(':', $item);
                $result[trim($key)] = trim(trim($value, ';'));
            }
        }
        return $result;
    }

    /**
     * Determine if the style items is empty.
     * @return bool
     */
    public function empty()
    {
        return empty($this->items);
    }

    /**
     * Return all of the style items.
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Build the data to string.
     * @return string
     */
    public function build()
    {
        $arr = [];
        foreach ($this->items as $key => $value) {
            $arr[] = sprintf("%s:%s;", $key, $value);
        }
        return implode(' ', $arr);
    }
}