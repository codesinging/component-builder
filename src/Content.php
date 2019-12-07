<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:31
 */

namespace CodeSinging\ComponentBuilder;

use Closure;

class Content extends Builder
{
    /**
     * All of the content items.
     * @var array
     */
    protected $items = [];

    /**
     * The glue to implode the content items.
     * @var string
     */
    protected $glue = '';

    /**
     * Content constructor.
     *
     * @param string|array|Builder|Closure $contents
     */
    public function __construct(...$contents)
    {
        $this->add(...$contents);
    }

    /**
     * Parse the content.
     *
     * @param string|Builder|Closure $content
     *
     * @return string
     */
    protected function parse($content)
    {
        if (empty($content)) {
            return '';
        }

        if ($content instanceof Closure) {
            $self = new self();
            $content = call_user_func($content, $self) ?? $self;
        }

        return (string)$content;
    }

    /**
     * Flatten the content array.
     *
     * @param array $contents
     *
     * @return array
     */
    protected function flatten(array $contents)
    {
        $array = [];
        foreach ($contents as $content) {
            if (!is_null($content)) {
                if (is_array($content)) {
                    $array = array_merge($array, $content);
                } else {
                    $array[] = $content;
                }
            }
        }
        return $array;
    }

    /**
     * Store value to property store.
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
     * Add content.
     *
     * @param string|array|Builder|Closure $contents
     *
     * @return $this
     */
    public function add(...$contents)
    {
        $contents = $this->flatten($contents);
        $contents and $this->items = array_merge($this->items, $contents);

        return $this;
    }

    /**
     * Prepend content.
     *
     * @param string|array|Builder|Closure $contents
     *
     * @return $this
     */
    public function prepend(...$contents)
    {
        $contents = $this->flatten($contents);
        $contents and $this->items = array_merge($contents, $this->items);
        return $this;
    }

    /**
     * Add content, and support binding and storing.
     *
     * @param string|array|Builder|Closure $content
     * @param string|null                  $bind
     * @param bool                         $store
     *
     * @return $this
     */
    public function content($content, string $bind = null, bool $store = false)
    {
        if ($bind) {
            $this->add(sprintf('{{ %s }}', $bind));
            $store and $this->store($bind, $content);
        } else {
            $this->add($content);
        }
        return $this;
    }

    /**
     * Determine if the content is empty.
     * @return bool
     */
    public function empty()
    {
        return empty($this->items);
    }

    /**
     * Set the glue of the content items.
     *
     * @param string $glue
     *
     * @return $this
     */
    public function glue(string $glue = PHP_EOL)
    {
        $this->glue = $glue;
        return $this;
    }

    /**
     * Set the glue to PHP_EOL.
     *
     * @param int $count
     *
     * @return $this
     */
    public function eol(int $count = 1)
    {
        $this->glue(str_repeat(PHP_EOL, $count));
        return $this;
    }

    /**
     * Add a new blank line.
     *
     * @return $this
     */
    public function addBlank()
    {
        $this->add('');
        return $this;
    }

    /**
     * Add a new `PHP_EOL` line.
     *
     * @return $this
     */
    public function addNewline()
    {
        $this->add(PHP_EOL);
        return $this;
    }

    /**
     * Get all the content items.
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Build content to a string.
     * @return string
     */
    public function build()
    {
        return implode($this->glue, array_map([$this, 'parse'], $this->items));
    }
}