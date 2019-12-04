<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:14
 */

namespace CodeSinging\ComponentBuilder;

use Closure;

class Element extends Builder
{
    use Directive;

    /**
     * The tag.
     * @var string
     */
    protected $tag;

    /**
     * The element Property instance.
     * @var Property
     */
    public $property;

    /**
     * If there is an end tag.
     * @var bool
     */
    protected $end = true;

    /**
     * If there is an 'End Of Line' between the begin tag and the end tag.
     * @var bool
     */
    protected $eol = false;

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
     * The Content instance.
     * @var Content
     */
    public $content;

    /**
     * The parent element instance.
     * @var Element
     */
    public $parent;

    /**
     * Builder constructor.
     *
     * @param string                 $tag
     * @param string|Builder|Closure $content
     * @param array|null             $properties
     * @param bool                   $end
     * @param bool                   $eol
     */
    public function __construct(string $tag = 'div', string $content = null, array $properties = null, bool $end = true, bool $eol = false)
    {
        $this->tag($tag);
        $this->content = new Content($content);
        $this->property = new Property($properties);
        $this->css = new Css();
        $this->style = new Style();
        $this->end($end);
        $this->eol($eol);
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
     * @return static
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
     * @param string|array|null $name
     * @param mixed|null        $value
     * @param string|array|null $bind
     * @param bool              $store
     *
     * @return $this
     */
    public function set($name, $value = null, $bind = null, bool $store = false)
    {
        $this->property->set($name, $value, $bind, $store);

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
                    $value = $key;
                }
                $this->set($key, $value);
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
        return $this->property->get($name, $default);
    }

    /**
     * Determine if there is an end tag.
     *
     * @param bool $end
     *
     * @return $this
     */
    public function end(bool $end = true)
    {
        $this->end = $end;
        return $this;
    }

    /**
     * Determine if there is an 'End Of Line' between the begin and end tag.
     *
     * @param bool $eol
     *
     * @return $this
     */
    public function eol(bool $eol = true)
    {
        $this->eol = $eol;
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
     * @param string|array|Builder|Closure ...$contents
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
     * @param string|array|Builder|Closure ...$contents
     *
     * @return $this
     */
    public function prepend(...$contents)
    {
        $this->content->prepend(...$contents);
        return $this;
    }

    /**
     * Add content, support binding and storing.
     *
     * @param string|array|Builder|Closure $content
     * @param string|null              $bind
     * @param bool                     $store
     *
     * @return $this
     */
    public function content($content, string $bind = null, bool $store = false)
    {
        $this->content->content($content, $bind, $store);

        return $this;
    }

    /**
     * Determine if the content is empty.
     * @return bool
     */
    public function empty()
    {
        return $this->content->empty();
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
     * @param bool           $eol
     *
     * @return $this
     */
    public function parent($tag = 'div', array $attributes = [], bool $eol = false)
    {
        if ($tag instanceof Closure) {
            $parent = new Element();
            $this->parent = call_user_func($tag, $parent) ?? $parent;
        } elseif ($tag instanceof Element) {
            $this->parent = $tag;
        } else {
            $this->parent = new Element($tag, '', $attributes, true, $eol);
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
            $this->set('class', $this->css->build());
        }
    }

    /**
     * Merge the style and the style property.
     */
    protected function mergeStyle()
    {
        if (!$this->style->empty()) {
            $this->style->prepend($this->get('style'));
            $this->set('style', $this->style->build());
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
     * Build the element.
     * @return string
     */
    public function build()
    {
        $this->__build();

        $content = $this->content->build();
        $this->mergeCss();
        $this->mergeStyle();

        $element = sprintf(
            '<%s%s>%s%s%s%s',
            $this->tag,
            $this->property->build(),
            $this->eol && !empty($content) ? PHP_EOL : '',
            $content,
            $this->eol && $this->end ? PHP_EOL : '',
            $this->end ? '</' . $this->tag . '>' : ''
        );

        if ($this->parent) {
            return $this->parent->add($element)->build();
        }

        return $element;
    }
}