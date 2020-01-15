<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:50
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Content;
use CodeSinging\ComponentBuilder\Store;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    public function testConstruct()
    {
        self::assertEquals('a', new Content('a'));
        self::assertEquals('ab', new Content('a', 'b'));
    }

    public function testContentIsString()
    {
        self::assertEquals('a', new Content('a'));
        self::assertEquals('ab', (new Content('a'))->add('b'));
    }

    public function testContentIsBuildable()
    {
        self::assertEquals('ab', (new Content('a'))->add(new Content('b')));
    }

    public function testContentIsClosure()
    {
        self::assertEquals('ab', new Content(function () {
            return 'ab';
        }));
        self::assertEquals('ab', new Content(function (Content $content) {
            $content->add('ab');
            return $content;
        }));
        self::assertEquals('ab', new Content(function (Content $content) {
            $content->add('ab');
        }));
    }

    public function testAdd()
    {
        self::assertEquals('abc', (new Content())->add('a', 'b', 'c'));
        self::assertEquals('abc', (new Content())->add(['a', 'b', 'c']));
        self::assertEquals('abc', (new Content())->add(['a', 'b'], 'c'));
    }

    public function testPrepend()
    {
        self::assertEquals('abc', (new Content('c'))->prepend('ab'));
        self::assertEquals('abc', (new Content('c'))->prepend('a', 'b'));
        self::assertEquals('abc', (new Content('c'))->prepend(['a'], 'b'));
        self::assertEquals('abc', (new Content('c'))->prepend(['a', 'b']));
    }

    public function testContent()
    {
        self::assertEquals('bc', (new Content('a'))->content('b', 'c'));
    }

    public function testInterpolation()
    {
        self::assertEquals('{{ name }}', (new Content())->interpolation('name'));
        self::assertEquals('{{ "id-"+id }}', (new Content())->interpolation('"id-"+id'));
    }

    public function testInterpolationAndStore()
    {
        self::assertEquals('{{ age }}', (new Content())->interpolation('age', 20));
        self::assertEquals(20, Store::get('age'));
    }

    public function testClear()
    {
        self::assertEquals('', (new Content('a'))->clear());
    }

    public function testEmpty()
    {
        self::assertTrue((new Content())->isEmpty());
        self::assertTrue((new Content(null))->isEmpty());
        self::assertFalse((new Content(''))->isEmpty());
        self::assertFalse((new Content('a'))->isEmpty());
    }

    public function testGlue()
    {
        $content = new Content('a', 'b', 'c');
        self::assertEquals('abc', $content);
        self::assertEquals('a b c', $content->glue(' '));
        self::assertEquals('a,b,c', $content->glue(','));
        self::assertEquals('a|b|c', $content->glue('|'));
    }

    public function testGlueLineBreak()
    {
        $content = new Content('a', 'b');
        self::assertEquals('a' . PHP_EOL . 'b', $content->glueLineBreak());
        self::assertEquals('a' . PHP_EOL . PHP_EOL . 'b', $content->glueLineBreak(2));
        self::assertEquals(PHP_EOL . 'a' . PHP_EOL . 'b', $content->prepend('')->glueLineBreak());
    }

    public function testAddBlank()
    {
        $content = new Content('a', 'b');
        $content->addBlank();
        $items = $content->all();

        self::assertEquals('', end($items));
    }

    public function testAddLineBreak()
    {
        $content = new Content('a', 'b');
        $content->addLineBreak();
        $items = $content->all();

        self::assertEquals(PHP_EOL, end($items));
    }

    public function testAll()
    {
        self::assertEquals(['a', 'b', 'c'], (new Content('a', ['b', 'c']))->all());
    }
}