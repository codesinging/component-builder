<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:17
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Css;
use PHPUnit\Framework\TestCase;

class CssTest extends TestCase
{
    protected function css($css = '', $mergeCss = '')
    {
        return new Css($css, $mergeCss);
    }

    public function testConstruct()
    {
        self::assertEquals('', $this->css());
        self::assertEquals('margin', $this->css('margin'));
        self::assertEquals('margin padding', $this->css('margin', 'padding'));
    }

    public function testReset()
    {
        self::assertEquals('', $this->css('margin')->reset());
        self::assertEquals('padding', $this->css('margin')->reset('padding'));
        self::assertEquals('padding', $this->css('margin')->reset()->add('padding'));
    }

    public function testAdd()
    {
        // string
        self::assertEquals('margin', $this->css()->add('margin'));
        self::assertEquals('margin padding', $this->css('margin')->add('padding'));
        self::assertEquals('margin padding', $this->css()->add('margin')->add('padding'));
        // array
        self::assertEquals('margin padding', $this->css(['margin', 'padding']));
        self::assertEquals('margin padding', $this->css()->add(['margin', 'padding']));
        self::assertEquals('margin padding', $this->css()->add(['margin'])->add('padding'));
        // Classes instance
        self::assertEquals('margin padding', $this->css('margin', 'padding'));
        self::assertEquals('margin padding', $this->css('margin')->add($this->css('padding')));
        // Closure
        self::assertEquals('margin padding', $this->css(function () {
            return 'margin padding';
        }));
        self::assertEquals('margin padding', $this->css(function (Css $css) {
            return $css->add('margin padding');
        }));
        self::assertEquals('margin padding', $this->css('margin')->add(function (Css $css) {
            $css->add('padding');
        }));
    }

    public function testPrepend()
    {
        self::assertEquals('margin padding', $this->css()->add('padding')->add('margin', true));
        self::assertEquals('margin padding', $this->css('padding')->prepend('margin'));
    }

    public function testEmpty()
    {
        self::assertTrue($this->css()->empty());
    }

    public function testAll()
    {
        self::assertEquals(['margin', 'padding'], $this->css('margin padding')->all());
    }

    public function testBuild()
    {
        $css = $this->css('margin', 'padding');
        self::assertEquals('margin padding', $css->build());
        self::assertEquals($css, $css->build());
        self::assertTrue($css == $css->build());
    }
}