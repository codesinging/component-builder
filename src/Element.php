<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:14
 */

namespace CodeSinging\ComponentBuilder;

use Closure;
use CodeSinging\Support\Str;

class Element implements Buildable
{
    use Directive;

    /**
     * The element tag.
     * @var string
     */
    protected $tag = '';

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
     * @var Element
     */
    public $parent;

    /**
     * The element's default attributes.
     * @var array
     */
    protected $attributes = [];

    /**
     * Builder constructor.
     *
     * @param string                         $tag
     * @param string|array|Buildable|Closure $content
     * @param array|null                     $attributes
     * @param bool                           $closing
     * @param bool                           $lineBreak
     */
    public function __construct(string $tag = 'div', $content = null, array $attributes = null, bool $closing = true, bool $lineBreak = false)
    {
        $this->tag($tag);
        $this->content = new Content($content);
        $this->attribute = new Attribute($this->attributes);
        $this->attribute->set($attributes);
        $this->css = new Css();
        $this->style = new Style();
        $this->closing($closing);
        $this->lineBreak($lineBreak);
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
     * @return Element
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
     * Set event attribute.
     *
     * @param array|string $name
     * @param null|string  $value
     *
     * @return $this
     */
    public function on($name, $value = null)
    {
        if (is_string($name)) {
            $name = [$name => $value];
        }
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                if (is_int($key)) {
                    list($key, $value) = [$value, null];
                }
                if (is_null($value)) {
                    $value = Str::camel($key);
                }

                $key = $this->attribute->fill($key, '@');
                $this->attribute->set($key, $value);
            }
        }
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
     * @param string                 $name
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
     * Set the glue of the content.
     *
     * @param string $glue
     *
     * @return $this
     */
    public function glue(string $glue = PHP_EOL)
    {
        $this->content->glue($glue);
        return $this;
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
     * Return a string.
     * @return string
     */
    public function __toString()
    {
        $this->__build();

        $this->mergeCss();
        $this->mergeStyle();

        $element = sprintf(
            '<%s%s>%s%s%s%s',
            $this->tag,
            $this->attribute->isEmpty() ? '': ' ' . (string)$this->attribute,
            $this->lineBreak && !$this->content->isEmpty() ? PHP_EOL : '',
            (string)$this->content,
            $this->lineBreak && $this->closing ? PHP_EOL : '',
            $this->closing ? '</' . $this->tag . '>' : ''
        );

        if ($this->parent instanceof self){
            return  (string)$this->parent->add($element);
        }

        return $element;
    }
}