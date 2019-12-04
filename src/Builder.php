<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:15
 */

namespace CodeSinging\ComponentBuilder;

abstract class Builder
{
    /**
     * Build the class to a string.
     * @return string
     */
    abstract public function build();

    /**
     * Return the class as a string.
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }
}