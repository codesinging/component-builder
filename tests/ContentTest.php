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

    public function testParams()
    {
        // string
        self::assertEquals('a', new Content('a'));
        self::assertEquals('ab', (new Content('a'))->add('b'));

        // Stringify
        self::assertEquals('ab', (new Content('a'))->add(new Content('b')));

        // Closure
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
        self::assertEquals('name', (new Content())->content('name'));
        self::assertEquals('{{ name }}', (new Content())->content('Name', 'name'));
        self::assertEquals('{{ age }}', (new Content())->content(11, 'age', true));
        self::assertEquals(11, Store::get('age'));
    }

    public function testEmpty()
    {
        self::assertTrue((new Content())->empty());
        self::assertTrue((new Content(null))->empty());
        self::assertFalse((new Content(''))->empty());
        self::assertFalse((new Content('a'))->empty());
    }

    public function testGlue()
    {
        $content = new Content('a', 'b', 'c');
        self::assertEquals('abc', $content);
        self::assertEquals('a b c', $content->glue(' '));
        self::assertEquals('a,b,c', $content->glue(','));
        self::assertEquals('a|b|c', $content->glue('|'));
    }

    public function testEol()
    {
        $content = new Content('a', 'b');
        self::assertEquals('a' . PHP_EOL . 'b', $content->eol());
        self::assertEquals('a' . PHP_EOL . PHP_EOL . 'b', $content->eol(2));
        self::assertEquals(PHP_EOL . 'a' . PHP_EOL . 'b', $content->prepend('')->eol());
    }

    public function testAddBlank()
    {
        $content = new Content('a', 'b');
        $content->addBlank();
        $items = $content->all();

        self::assertEquals('', end($items));
    }

    public function testAddNewline()
    {
        $content = new Content('a', 'b');
        $content->addNewline();
        $items = $content->all();

        self::assertEquals(PHP_EOL, end($items));
    }

    public function testAll()
    {
        self::assertEquals(['a', 'b', 'c'], (new Content('a', ['b', 'c']))->all());
    }
}