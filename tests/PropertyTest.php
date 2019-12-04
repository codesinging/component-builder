<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:13
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Property;
use CodeSinging\ComponentBuilder\Store;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    protected $props = [
        'id' => '1',
        'data-id' => 2,
        'disabled',
        'loading' => null,
        'status' => true,
        'border' => false,
        ':model' => 'model',
        '@click' => 'onClick',
    ];

    protected function prop(array $attributes = [])
    {
        return new Property($attributes);
    }

    public function testHas()
    {
        $attr = $this->prop($this->props);

        self::assertTrue($attr->has('id'));
        self::assertTrue($attr->has('data-id'));
        self::assertTrue($attr->has('disabled'));
        self::assertTrue($attr->has('loading'));
        self::assertTrue($attr->has('status'));
        self::assertTrue($attr->has('border'));
        self::assertTrue($attr->has(':model'));
        self::assertTrue($attr->has('@click'));
    }

    public function testGet()
    {
        $prop = $this->prop($this->props);
        self::assertEquals('1', $prop->get('id'));
        self::assertEquals(1, $prop->get('page', 1));
        self::assertEquals(2, $prop->get('data-id'));
        self::assertEquals(null, $prop->get('disabled'));
        self::assertEquals(null, $prop->get('loading'));
        self::assertEquals(true, $prop->get('status'));
        self::assertEquals(false, $prop->get('border'));
        self::assertEquals('model', $prop->get(':model'));
        self::assertEquals('onClick', $prop->get('@click'));
    }

    public function testSet()
    {
        self::assertEquals(['id' => 1], $this->prop()->set('id', 1)->all());
        self::assertEquals(['id' => 1], $this->prop()->set(['id' => 1])->all());
        self::assertEquals(['data-id' => '2'], $this->prop()->set('data-id', '2')->all());
        self::assertEquals(['loading' => null], $this->prop()->set('loading')->all());
        self::assertEquals(['loading' => null], $this->prop()->set('loading', null)->all());
        self::assertEquals(['loading' => null], $this->prop()->set(['loading'])->all());
        self::assertEquals(['status' => true], $this->prop()->set(['status' => true])->all());
        self::assertEquals(['status' => false], $this->prop()->set(['status' => false])->all());

        self::assertEquals([':model' => 'model'], $this->prop()->set([':model' => 'model'])->all());
        self::assertEquals(['@click' => 'onClick'], $this->prop()->set(['@click' => 'onClick'])->all());
    }

    public function testSetBind()
    {
        self::assertEquals([':id' => 'id'], $this->prop()->set('id', 1, 'id')->all());
        self::assertEquals([':id' => 'id'], $this->prop()->set(['id' => 1], ['id' => 'id'])->all());
        self::assertEquals([':id' => 'id', 'cid' => 2], $this->prop()->set(['id' => 1, 'cid' => 2], ['id' => 'id'])->all());
        self::assertEquals([':id' => 'id', ':cid' => 'cate.id'], $this->prop()->set(['id' => 1, 'cid' => 2], ['id' => 'id', 'cid' => 'cate.id'])->all());
    }

    public function testSetBindStore()
    {
        $prop = $this->prop();
        $prop->set('id', 11, 'id', true);
        $prop->set(['cid' => 22, 'pid' => 33], ['cid' => 'cate.id'], ['cid', 'pid']);

        self::assertEquals([':id' => 'id', ':cid' => 'cate.id', 'pid' => 33], $prop->all());
        self::assertEquals(11, Store::get('id'));
        self::assertEquals(22, Store::get('cate.id'));
        self::assertArrayNotHasKey('pid', Store::all());
    }

    public function testEmpty()
    {
        self::assertTrue($this->prop()->empty());
        self::assertTrue($this->prop([])->empty());
        self::assertFalse($this->prop(['loading'])->empty());
    }

    public function testAll()
    {
        self::assertEquals(['id' => 'app'], ($this->prop(['id' => 'app']))->all());
        self::assertEquals(['id' => 'app'], ($this->prop())->set(['id' => 'app'])->all());
        self::assertEquals(['id' => 'app', 'disabled' => null], ($this->prop(['id' => 'app']))->set('disabled')->all());
        self::assertEquals(['id' => 'app', 'disabled' => true], ($this->prop(['id' => 'app']))->set('disabled', true)->all());
        self::assertEquals(['id' => 'app', 'disabled' => false], ($this->prop(['id' => 'app']))->set('disabled', false)->all());
    }

    public function testCreate()
    {
        self::assertEquals('id="1"', $this->prop()->create('id', 1));
        self::assertEquals('id="1"', $this->prop()->create('id', "1"));
        self::assertEquals('disabled="1"', $this->prop()->create('disabled', true));
        self::assertEquals('disabled=""', $this->prop()->create('disabled', false));
        self::assertEquals('disabled', $this->prop()->create('disabled', null));
        self::assertEquals('disabled', $this->prop()->create('disabled'));
    }

    public function testBuild()
    {
        self::assertEquals('', $this->prop());
        self::assertEquals(' id="1"', $this->prop()->set('id', 1));
        self::assertEquals(' id="1" data-id="2"', $this->prop()->set('id', 1)->set('data-id', 2));
    }
}