<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:25
 */

namespace CodeSinging\ComponentBuilder;

use CodeSinging\Support\Str;

class Component extends Element
{
    /**
     * The component tag prefix.
     * @var string
     */
    protected $tagPrefix = '';

    /**
     * The component's base tag.
     * @var string
     */
    protected $baseTag;

    /**
     * Component constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($this->tagPrefix . $this->baseTag(), null, $attributes, true, false);
    }

    /**
     * Get the component's base tag.
     * @return string
     */
    public function baseTag()
    {
        return $this->baseTag ?: Str::kebab((basename(str_replace('\\', '/', get_class($this)))));
    }

    /**
     * Handle dynamic calls to the component to set attributes.
     *
     * @param $name
     * @param $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $name = Str::kebab($name);
        $this->set($name, $arguments[0] ?? true, $arguments[1] ?? null);

        return $this;
    }
}
