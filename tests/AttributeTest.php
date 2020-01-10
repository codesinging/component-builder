<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2020/1/8 22:24
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Attribute;
use CodeSinging\ComponentBuilder\Store;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{

    public function testHas()
    {
        $attr = new Attribute(['id' => 1, 'disabled', 'loading' => true]);

        self::assertTrue($attr->has('id'));
        self::assertTrue($attr->has('disabled'));
        self::assertTrue($attr->has('loading'));
    }

    public function testGet()
    {
        $attr = new Attribute(['id' => 1, 'disabled', 'loading' => true]);

        self::assertEquals(1, $attr->get('id'));
        self::assertEquals(null, $attr->get('disabled'));
        self::assertEquals(true, $attr->get('loading'));
    }

    public function testSetNameIsStringAndValueIsCommonString()
    {
        $attr = new Attribute();
        $attr->set('id', 'app');
        self::assertEquals(['id' => 'app'], $attr->items());
        self::assertEquals(['id' => 'app'], $attr->data());
    }

    public function testSetNameIsStringAndValueStartedWithColon()
    {
        $attr = new Attribute();
        $attr->set('age', ':age');
        self::assertEquals([':age' => 'age'], $attr->data());
        self::assertEquals([':age' => 'age'], $attr->items());
    }

    public function testSetNameIsStringAndValueStartedWithBackslashAndColon()
    {
        $attr = new Attribute();
        $attr->set('age', '\:age');
        self::assertEquals(['age' => ':age'], $attr->data());
        self::assertEquals(['age' => ':age'], $attr->items());
    }

    public function testSetNameIsStringAndValueStartedWithDoubleBackslash()
    {
        $attr = new Attribute();
        $attr->set('age', '\\\:age');
        self::assertEquals(['age' => '\:age'], $attr->data());
        self::assertEquals(['age' => '\:age'], $attr->items());
    }

    public function testSetNameIsStringAndValueIsTrue()
    {
        $attr = new Attribute();
        $attr->set('disabled', true);
        self::assertEquals(['disabled' => true], $attr->data());
        self::assertEquals([':disabled' => 'true'], $attr->items());
    }

    public function testSetNameIsStringAndValueIsFalse()
    {
        $attr = new Attribute();
        $attr->set('disabled', false);
        self::assertEquals(['disabled' => false], $attr->data());
        self::assertEquals([':disabled' => 'false'], $attr->items());
    }

    public function testSetNameIsStringAndValueIsInt()
    {
        $attr = new Attribute();
        $attr->set('age', 10);
        self::assertEquals(['age' => 10], $attr->data());
        self::assertEquals([':age' => 10], $attr->items());
    }

    public function testSetNameIsStringAndValueIsArray()
    {
        $attr = new Attribute();
        $attr->set('data', ['age' => 10]);
        self::assertEquals(['data' => ['age' => 10]], $attr->data());
        self::assertEquals([':data' => 'attributeStores.data'], $attr->items());
        self::assertEquals(['age' => 10], Store::get('attributeStores.data'));
    }

    public function testSetNameIsStringAndWithStore()
    {
        $attr = new Attribute();
        $attr->set('disabled', 'disabled', true);

        self::assertEquals([':disabled' => 'disabled'], $attr->items());
        self::assertEquals(['disabled' => 'disabled'], $attr->data());
        self::assertEquals(true, Store::get('disabled'));
    }

    public function testSetNameIsArray()
    {
        $attr = new Attribute();
        $attr->set([
            'age' => 20,
            'sex' => 'male',
            'disabled',
            'passed' => true,
        ]);

        self::assertEquals([
            'age' => 20,
            'sex' => 'male',
            'disabled' => null,
            'passed' => true,
        ], $attr->data());
        self::assertEquals([
            ':age' => 20,
            'sex' => 'male',
            'disabled' => null,
            ':passed' => 'true',
        ], $attr->items());
    }

    public function testSetNameIsArrayAndValueIsArrayAndWithoutBind()
    {
        $attr = new Attribute();
        $attr->set([
            'age' => 20,
            'sex' => 'sex',
            'disabled',
            'passed' => true,
        ], [
            'sex' => 'male',
        ]);
        self::assertEquals([
            'age' => 20,
            'sex' => 'sex',
            'disabled' => null,
            'passed' => true,
        ], $attr->data());
        self::assertEquals([
            ':age' => 20,
            ':sex' => 'sex',
            'disabled' => null,
            ':passed' => 'true',
        ], $attr->items());
        self::assertEquals('male', Store::get('sex'));
    }

    public function testFill()
    {
        $attr = new Attribute();
        self::assertEquals('name', $attr->fill('name'));
        self::assertEquals(':name', $attr->fill('name', ':'));
        self::assertEquals(':name', $attr->fill(':name', ':'));
        self::assertEquals('@click', $attr->fill('click', '@'));
        self::assertEquals('@click', $attr->fill('@click', '@'));
    }

    public function testEmpty()
    {
        self::assertTrue((new Attribute())->isEmpty());
    }

    public function testCreate()
    {
        self::assertEquals('disabled', (new Attribute())->create('disabled'));
        self::assertEquals('disabled', (new Attribute())->create('disabled', null));
        self::assertEquals('id="app"', (new Attribute())->create('id', 'app'));
        self::assertEquals('cid="2"', (new Attribute())->create('cid', 2));
    }

    public function testToString()
    {
        self::assertEquals('disabled id="app"', new Attribute(['disabled', 'id' => 'app']));
    }
}