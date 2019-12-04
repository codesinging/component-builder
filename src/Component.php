<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:25
 */

namespace CodeSinging\ComponentBuilder;

use CodeSinging\Helpers\Str;

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
     * @param array $props
     */
    public function __construct(array $props = [])
    {
        parent::__construct($this->tagPrefix . $this->baseTag(), null, $props, true, false);
    }

    /**
     * Get the component's base tag.
     * @return string
     */
    public function baseTag()
    {
        return $this->baseTag ?: Str::kebab((basename(str_replace('\\', '/', get_class($this)))));
    }
}
