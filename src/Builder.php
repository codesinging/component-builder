<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:14
 */

namespace CodeSinging\ComponentBuilder;

use Closure;
use CodeSinging\Support\Repository;
use CodeSinging\Support\Str;

class Builder implements Buildable
{
    use Directive;

    /**
     * The element tag.
     * @var string
     */
    protected $tag = 'div';

    /**
     * The Attribute instance.
     * @var Attribute
     */
    protected $attribute;

    /**
     * The Content instance.
     * @var Content
     */
    protected $content;

    /**
     * If the element has a closing tag.
     * @var bool
     */
    protected $closing = true;

    /**
     * If the element has line break between the opening tag, content and the closing tag.
     * @var bool
     */
    protected $lineBreak = false;

    /**
     * The Css instance.
     * @var Css
     */
    public $css;

    /**
     * The Style instance.
     * @var Style
     */
    public $style;

    /**
     * The parent element instance.
     * @var Builder
     */
    public $parent;

    /**
     * The element's default attributes.
     * @var array
     */
    protected $attributes = [];

    /**
     * The builder id.
     * @var int
     */
    protected $builderId;

    /**
     * The builder count.
     * @var int
     */
    protected static $builderCount = 0;

    /**
     * The builder config.
     * @var Repository
     */
    protected $config;

    /**
     * Builder constructor.
     *
     * @param string                         $tag
     * @param string|array|Buildable|Closure $content
     * @param array|null                     $attributes
     * @param bool                           $closing
     * @param bool                           $lineBreak
     * @param string|bool                    $glue
     */
    public function __construct(string $tag = 'div', $content = null, array $attributes = null, bool $closing = true, bool $lineBreak = false, $glue = '')
    {
        $this->tag($tag);
        $this->content = new Content($content);
        $this->attribute = new Attribute($this->attributes);
        $this->attribute->set($attributes);
        $this->css = new Css();
        $this->style = new Style();
        $this->closing($closing);
        $this->lineBreak($lineBreak);
        $this->glue($glue);
        $this->builderId = ++self::$builderCount;
        $this->config = new Repository();
        $this->__init();
    }

    /**
     * Do something when the Element initialize.
     */
    protected function __init()
    {
        // Rewrite the method to do something.
    }

    /**
     * Get a instance.
     *
     * @param mixed ...$parameters
     *
     * @return Builder
     */
    public static function instance(...$parameters)
    {
        return new static(...$parameters);
    }

    /**
     * Set tag.
     *
     * @param string $tag
     *
     * @return $this
     */
    public function tag(string $tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * Set properties.
     *
     * @param string|array     $name
     * @param mixed|array|null $value
     * @param mixed|null       $store
     *
     * @return $this
     */
    public function set($name, $value = null, $store = null)
    {
        $this->attribute->set($name, $value, $store);
        return $this;
    }

    /**
     * Get the specified property value.
     *
     * @param string     $name
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->attribute->get($name, $default);
    }

    /**
     * Determine if there is an end tag.
     *
     * @param bool $closing
     *
     * @return $this
     */
    public function closing(bool $closing = true)
    {
        $this->closing = $closing;
        return $this;
    }

    /**
     * Determine if there is an 'End Of Line' between the begin and end tag.
     *
     * @param bool $eol
     *
     * @return $this
     */
    public function lineBreak(bool $eol = true)
    {
        $this->lineBreak = $eol;
        return $this;
    }

    /**
     * Set the builder's glue to implode the content items.
     *
     * @param bool|string $glue
     *
     * @return $this
     */
    public function glue($glue = true)
    {
        $this->content->glue($glue);
        return $this;
    }

    /**
     * Add css classes.
     *
     * @param string|array|Css|Closure $css
     * @param bool                     $prepend
     *
     * @return $this
     */
    public function css($css, bool $prepend = false)
    {
        $this->css->add($css, $prepend);
        return $this;
    }

    /**
     * Add styles.
     *
     * @param array|string|Style|Closure $styles
     * @param bool                       $prepend
     *
     * @return $this
     */
    public function style($styles, bool $prepend = false)
    {
        $this->style->add($styles, $prepend);
        return $this;
    }

    /**
     * Add contents.
     *
     * @param string|array|Buildable|Closure ...$contents
     *
     * @return $this
     */
    public function add(...$contents)
    {
        $this->content->add(...$contents);
        return $this;
    }

    /**
     * Prepend contents.
     *
     * @param string|array|Buildable|Closure ...$contents
     *
     * @return $this
     */
    public function prepend(...$contents)
    {
        $this->content->prepend(...$contents);
        return $this;
    }

    /**
     * Clear existed content items and then add contents.
     *
     * @param string|array|Buildable|Closure ...$contents
     *
     * @return $this
     */
    public function content(...$contents)
    {
        $this->content->content(...$contents);
        return $this;
    }

    /**
     * Add a text interpolation, and support store default value.
     *
     * @param string     $content
     * @param mixed|null $store
     *
     * @return $this
     */
    public function interpolation($content, $store = null)
    {
        $this->content->interpolation($content, $store);

        return $this;
    }

    /**
     * Add a named slot to the content.
     *
     * @param string                   $name
     * @param string|Buildable|Closure $content
     *
     * @return $this
     */
    public function slot(string $name, $content)
    {
        if (is_string($content)) {
            $content = new self('template', $content, ['slot' => $name]);
        } elseif ($content instanceof self) {
            $content->set('slot', $name);
        } elseif ($content instanceof Closure) {
            $content = call_user_func($content);
            $content->set('slot', $name);
        }

        $this->add($content);

        return $this;
    }

    /**
     * Determine if the element content is empty.
     * @return bool
     */
    public function isEmpty()
    {
        return $this->content->isEmpty();
    }

    /**
     * Set the element's parent element.
     *
     * @param string|Closure $tag
     * @param array          $attributes
     * @param bool           $lineBreak
     *
     * @return $this
     */
    public function parent($tag = 'div', array $attributes = [], bool $lineBreak = false)
    {
        if ($tag instanceof Closure) {
            $parent = new self();
            $this->parent = call_user_func($tag, $parent) ?? $parent;
        } elseif ($tag instanceof self) {
            $this->parent = $tag;
        } else {
            $this->parent = new self($tag, '', $attributes, true, $lineBreak);
        }
        return $this;
    }

    /**
     * Get the builder id.
     *
     * @param string|null $prefix
     *
     * @return int|string
     */
    public function builderId(string $prefix = null)
    {
        return $prefix ? ($prefix . '_' . $this->builderId) : $this->builderId;
    }

    /**
     * Get or set builder config.
     *
     * @param string|array $key
     * @param mixed|null   $default
     *
     * @return $this|mixed
     */
    public function config($key, $default = null)
    {
        if (is_array($key)) {
            $this->config->set($key);
            return $this;
        } else {
            return $this->config->get($key, $default);
        }
    }

    /**
     * Store value to the Stores or get value from the Stores.
     *
     * @param string|array $name
     * @param null|mixed   $default
     *
     * @return $this|mixed
     */
    public function store($name, $default = null)
    {
        if (is_array($name)) {
            Store::set($name);
        } else {
            return Store::get($name, $default);
        }
        return $this;
    }

    /**
     * Set whether the component is buildable.
     *
     * @param bool $buildable
     *
     * @return $this
     */
    public function buildable(bool $buildable = true)
    {
        $this->config(compact('buildable'));
        return $this;
    }

    /**
     * Get whether the component is buildable.
     * @return bool
     */
    public function isBuildable()
    {
        return $this->config('buildable', true);
    }

    /**
     * Handle dynamic calls to the builder to set attributes.
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

    /**
     * Merge the css and the class property.
     */
    protected function mergeCss()
    {
        if (!$this->css->empty()) {
            $this->css->prepend($this->get('class'));
            $this->set('class', (string)$this->css);
        }
    }

    /**
     * Merge the style and the style property.
     */
    protected function mergeStyle()
    {
        if (!$this->style->empty()) {
            $this->style->prepend($this->get('style'));
            $this->set('style', (string)$this->style);
        }
    }

    /**
     * Do something before building the element.
     */
    protected function __build()
    {
        // Rewrite the method to do something.
    }

    /**
     * Build the builder.
     * @return string
     */
    public function build()
    {
        $this->__build();

        $this->mergeCss();
        $this->mergeStyle();

        $element = sprintf(
            '<%s%s>%s%s%s%s',
            $this->tag,
            $this->attribute->isEmpty() ? '' : ' ' . (string)$this->attribute,
            $this->lineBreak && !$this->content->isEmpty() ? PHP_EOL : '',
            (string)$this->content,
            $this->lineBreak && $this->closing ? PHP_EOL : '',
            $this->closing ? '</' . $this->tag . '>' : ''
        );

        if ($this->parent instanceof self) {
            return (string)$this->parent->add($element);
        }

        return $element;
    }

    /**
     * Return a string.
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }
}