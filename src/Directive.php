<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:19
 */

namespace CodeSinging\ComponentBuilder;

use CodeSinging\Helpers\Str;

trait Directive
{
    /**
     * Add `v-text` directive.
     *
     * @param string $text
     *
     * @return $this
     */
    public function vText(string $text)
    {
        $this->set('v-text', $text);
        return $this;
    }

    /**
     * Add `v-html` directive.
     *
     * @param string $html
     *
     * @return $this
     */
    public function vHtml(string $html)
    {
        $this->set('v-html', $html);
        return $this;
    }

    /**
     * Add `v-show` directive.
     *
     * @param string $condition
     *
     * @return $this
     */
    public function vShow(string $condition)
    {
        $this->set('v-show', $condition);
        return $this;
    }

    /**
     * Add `v-if` directive.
     *
     * @param string $condition
     *
     * @return $this
     */
    public function vIf(string $condition)
    {
        $this->set('v-if', $condition);
        return $this;
    }

    /**
     * Add `v-else` directive.
     *
     * @return $this
     */
    public function vElse()
    {
        $this->set('v-else');
        return $this;
    }

    /**
     * Add `v-if` directive.
     *
     * @param string $condition
     *
     * @return $this
     */
    public function vElseIf(string $condition)
    {
        $this->set('v-else-if', $condition);
        return $this;
    }

    /**
     * Add `v-for` directive.
     *
     * @param string $directive
     *
     * @return $this
     */
    public function vFor(string $directive)
    {
        $this->set('v-for', $directive);
        return $this;
    }

    /**
     * Add `v-on` directive.
     *
     * @param string|array $event
     * @param string|null  $handler
     *
     * @return $this
     */
    public function vOn($event, string $handler = null)
    {
        if (is_string($event)) {
            $event = [$event => $handler];
        }
        if (is_array($event)) {
            foreach ($event as $key => $value) {
                if (is_int($key)) {
                    list($key, $value) = [$value, null];
                }
                if (is_null($value)) {
                    $value = Str::camel($key);
                }
                $key = Str::start($key, '@');
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Add a `v-on:click` directive.
     *
     * @param string      $handler
     * @param string|null $modifier
     *
     * @return $this
     */
    public function vClick(string $handler, string $modifier = null)
    {
        $event = 'click' . ($modifier ? '.' . $modifier : '');
        $this->vOn($event, $handler);
        return $this;
    }

    /**
     * Add a `v-on:click` directive, and the handler assign a specific value to a property.
     *
     * @param string      $prop
     * @param             $value
     * @param string|null $modifier
     *
     * @return $this
     */
    public function vClickBind(string $prop, $value, string $modifier = null)
    {
        if (is_string($value)) {
            $value = "'$value'";
        } elseif ($value === true) {
            $value = 'true';
        } elseif ($value === false) {
            $value = 'false';
        }

        $handler = sprintf('%s = %s', $prop, $value);
        $this->vClick($handler, $modifier);

        return $this;
    }

    /**
     * Add `v-model` directive.
     *
     * @param string $model
     * @param string $modifier
     *
     * @return $this
     */
    public function vModel(string $model, string $modifier = '')
    {
        $modifier = Str::start($modifier, '.');
        $this->set('v-model' . $modifier, $model);
        return $this;
    }

    /**
     * Set 'ref' attribute.
     *
     * @param string $ref
     *
     * @return $this
     */
    public function ref($ref)
    {
        $this->set('ref', $ref);
        return $this;
    }
}