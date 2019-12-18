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