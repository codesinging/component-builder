<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:20
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Style;
use PHPUnit\Framework\TestCase;

class StyleTest extends TestCase
{
    protected function style($styles = '', $mergeStyles = '')
    {
        return new Style($styles, $mergeStyles);
    }

    public function testConstruct()
    {
        self::assertEquals('', $this->style());
        self::assertEquals('width:100px;', $this->style('width: 100px'));
        self::assertEquals('width:100px; height:100px;', $this->style('width: 100px', 'height: 100px'));
    }

    public function testReset()
    {
        self::assertEquals('', $this->style('width:100px')->reset());
        self::assertEquals('width:100px;', $this->style('height:100px')->reset('width:100px'));
        self::assertEquals('width:100px;', $this->style('height:100px')->reset()->add('width:100px'));
    }

    public function testAdd()
    {
        self::assertEquals('width:100px;', $this->style()->add('width:100px'));
        self::assertEquals('width:100px;', $this->style()->add('width:100px;'));
        self::assertEquals('width:100px;', $this->style()->add('width: 100px;'));
        self::assertEquals('width:100px;', $this->style()->add('width: 100px ;'));
        self::assertEquals('width:100px;', $this->style()->add('width: 100px ; '));
        self::assertEquals('width:100px;', $this->style()->add(['width' => '100px']));
        self::assertEquals('width:100px; height:100px;', $this->style()->add(['width' => '100px', 'height' => '100px']));
        self::assertEquals('width:100px; height:100px;', $this->style(['width' => '100px'])->add(['height' => '100px']));
        self::assertEquals('width:100px;', $this->style(function () {
            return ['width' => '100px'];
        }));
        self::assertEquals('width:100px;', $this->style(function (Style $style) {
            $style->add(['width' => '100px']);
        }));
        self::assertEquals('width:100px;', $this->style(function (Style $style) {
            return $style->add(['width' => '100px']);
        }));
        self::assertEquals('width:100px;', $this->style($this->style('width:100px')));
    }

    public function testPrepend()
    {
        self::assertEquals('width:100px; height:100px;', $this->style('height:100px')->prepend('width:100px'));
        self::assertEquals('width:100px; height:100px;', $this->style('height:100px')->add('width:100px', true));
    }

    public function testEmpty()
    {
        self::assertTrue($this->style()->empty());
    }

    public function testAll()
    {
        self::assertEquals(['width' => '100px'], $this->style('width: 100px;')->all());
    }
}