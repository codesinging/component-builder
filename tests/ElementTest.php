<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:21
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Element;
use CodeSinging\ComponentBuilder\Store;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{
    public function testInstance()
    {
        self::assertInstanceOf(Element::class, Element::instance());
    }

    public function testTag()
    {
        self::assertEquals('<div></div>', new Element('div'));
        self::assertEquals('<i></i>', new Element('i'));
        self::assertEquals('<b></b>', (new Element('i'))->tag('b'));
    }

    public function testSet()
    {
        self::assertEquals('<div id="app"></div>', new Element('div', null, ['id' => 'app']));
        self::assertEquals('<div id="app"></div>', (new Element())->set('id', 'app'));
        self::assertEquals('<div id="app"></div>', (new Element())->set(['id' => 'app']));
        self::assertEquals('<div id="app" :data-id="1"></div>', new Element('div', null, ['id' => 'app', 'data-id' => 1]));
        self::assertEquals('<div id="app" :data-id="1"></div>', (new Element())->set(['id' => 'app', 'data-id' => 1]));
    }

    public function testSetAndBind()
    {
        self::assertEquals('<div :id="id"></div>', (new Element())->set('id', ':id'));
        self::assertEquals('<div :id="id"></div>', (new Element())->set(['id' => ':id']));
        self::assertEquals('<div :id="id" pid="pid"></div>', (new Element())->set(['id' => ':id', 'pid' => 'pid']));
        self::assertEquals('<div :id="id" :pid="parent.id"></div>', (new Element())->set(['id' => ':id', 'pid' => ':parent.id']));
        self::assertEquals('<div :id="stores.id"></div>', (new Element())->set('id', ':stores.id'));
    }

    public function testSetAndBindAndStore()
    {
        self::assertEquals('<div :id="id"></div>', (new Element())->set('id',  'id', 11));
        self::assertEquals('<div :bid="book.id"></div>', (new Element())->set('bid',  'book.id', 99));
        self::assertEquals('<div :cid="cid"></div>', (new Element())->set(['cid' => 'cid'], ['cid' => 22]));
        self::assertEquals('<div :uid="uid" :vid="v.id"></div>', (new Element())->set(['uid' => ':uid', 'vid' => 'v.id'], ['vid' => 66]));

        self::assertEquals(11, Store::get('id'));
        self::assertEquals(99, Store::get('book.id'));
        self::assertEquals(22, Store::get('cid'));
        self::assertEquals(66, Store::get('v.id'));
    }

    public function testOn()
    {
        self::assertEquals('<button @click="onClick"></button>', (new Element('button'))->on('click', 'onClick'));
        self::assertEquals('<button @click="click"></button>', (new Element('button'))->on('click'));
        self::assertEquals('<button @on-change="onChange"></button>', (new Element('button'))->on('on-change'));
        self::assertEquals('<button @click="click" @on-change="onChange"></button>', (new Element('button'))->on(['click' => 'click', 'on-change' => 'onChange']));
        self::assertEquals('<button @click="click" @on-change="onChange"></button>', (new Element('button'))->on(['click', 'on-change']));
    }

    public function testGet()
    {
        self::assertEquals(100, (new Element('div'))->set('id', 100)->get('id'));
    }

    public function testClosing()
    {
        self::assertEquals('<div></div>', new Element('div'));
        self::assertEquals('<input>', (new Element('input', '', null, false)));
        self::assertEquals('<input>', (new Element('input'))->closing(false));
    }

    public function testLineBreak()
    {
        self::assertEquals('<div>a</div>', new Element('div', 'a'));
        self::assertEquals('<div>' . PHP_EOL . 'a' . PHP_EOL . '</div>', new Element('div', 'a', null, true, true));
        self::assertEquals('<div>' . PHP_EOL . '</div>', (string)(new Element('div', null, null, true, true)));
    }

    public function testCss()
    {
        self::assertEquals('<div class="margin"></div>', (new Element())->css('margin'));
        self::assertEquals('<div class="margin"></div>', (new Element())->css('margin'));
        self::assertEquals('<div class="margin padding"></div>', (new Element())->set('class', 'margin')->css('padding'));
    }

    public function testStyle()
    {
        self::assertEquals('<div style="color:white;"></div>', (new Element())->style('color:white'));
    }

    public function testAdd()
    {
        self::assertEquals('<div>ab</div>', (new Element('div', 'a'))->add('b'));
        self::assertEquals('<div>ab</div>', (new Element('div'))->add('a', 'b'));
    }

    public function testPrepend()
    {
        self::assertEquals('<div>ba</div>', (new Element('div', 'a'))->prepend('b'));
        self::assertEquals('<div>abc</div>', (new Element('div', 'c'))->prepend('a', 'b'));
    }

    public function testInterpolation()
    {
        self::assertEquals('<div>{{ name }}</div>', (new Element('div'))->interpolation('name'));
        self::assertEquals('<div>{{ age }}</div>', (new Element('div'))->interpolation('age', 20));
        self::assertEquals(20, Store::get('age'));
    }

    public function testSlot()
    {
        self::assertEquals('<div><template slot="header">Header</template></div>', (new Element())->slot('header', 'Header'));
    }

    public function testEmpty()
    {
        self::assertTrue((new Element())->isEmpty());
        self::assertFalse((new Element())->add('ab')->isEmpty());
    }

    public function testGlue()
    {
        self::assertEquals('<div>a' . PHP_EOL . 'b</div>', (new Element())->add('a', 'b')->glue());
    }

    public function testParent()
    {
        self::assertEquals('<div><i>inner</i></div>', (string)(new Element('i'))->parent('div')->add('inner'));
        self::assertEquals('<div class="outer"><i>inner</i></div>', (new Element('i'))->add('inner')->parent(function (Element $parent) {
            $parent->css('outer');
        }));
    }

    public function testVText()
    {
        self::assertEquals('<div v-text="msg"></div>', (new Element())->vText('msg'));
    }

    public function testVHtml()
    {
        self::assertEquals('<div v-html="html"></div>', (new Element())->vHtml('html'));
    }

    public function testVShow()
    {
        self::assertEquals('<div v-show="show"></div>', (new Element())->vShow('show'));
    }

    public function testVIf()
    {
        self::assertEquals('<div v-if="condition"></div>', (new Element())->vIf('condition'));
    }

    public function testVElse()
    {
        self::assertEquals('<div v-else></div>', (new Element())->vElse());
    }

    public function testVElseIf()
    {
        self::assertEquals('<div v-else-if="condition"></div>', (new Element())->vElseIf('condition'));
    }

    public function testVFor()
    {
        self::assertEquals('<div v-for="item in items"></div>', (new Element())->vFor('item in items'));
    }

    public function testVOn()
    {
        self::assertEquals('<button @click="onClick"></button>', (new Element('button'))->vOn('click', 'onClick'));
        self::assertEquals('<button @click="click"></button>', (new Element('button'))->vOn('click'));
        self::assertEquals('<button @on-change="onChange"></button>', (new Element('button'))->vOn('on-change'));
        self::assertEquals('<button @click="click" @on-change="onChange"></button>', (new Element('button'))->vOn(['click' => 'click', 'on-change' => 'onChange']));
        self::assertEquals('<button @click="click" @on-change="onChange"></button>', (new Element('button'))->vOn(['click', 'on-change']));
    }

    public function testVClick()
    {
        self::assertEquals('<button @click="onClick"></button>', (new Element('button'))->vClick('onClick'));
        self::assertEquals('<button @click.stop="onClick"></button>', (new Element('button'))->vClick('onClick', 'stop'));
    }

    public function testVClickBind()
    {
        self::assertEquals('<button @click="message = \'hello world\'"></button>', (new Element('button'))->vClickBind('message', 'hello world'));
        self::assertEquals('<button @click="age = 20"></button>', (new Element('button'))->vClickBind('age', 20));
        self::assertEquals('<button @click="visible = true"></button>', (new Element('button'))->vClickBind('visible', true));
        self::assertEquals('<button @click="visible = false"></button>', (new Element('button'))->vClickBind('visible', false));
    }

    public function testVModel()
    {
        self::assertEquals('<input v-model="name">', (new Element('input'))->closing(false)->vModel('name'));
        self::assertEquals('<input v-model.number="age">', (new Element('input'))->closing(false)->vModel('age', 'number'));
    }

    public function testRef()
    {
        self::assertEquals('<input ref="name">', (new Element('input'))->closing(false)->ref('name'));
    }

    public function testInit()
    {
        self::assertEquals('<i class="icon-home"></i>', new ExampleInitElement());
    }

    public function testAttributes()
    {
        self::assertEquals('<input type="password">', (new Element('input', null, ['type' => 'password'], false)));
        self::assertEquals('<input type="password">', new PasswordInput('input', null, [], false));
    }

    public function testBuild()
    {
        self::assertEquals('<span>__build</span>', new ExampleBuildElement('div'));
    }
}

class ExampleInitElement extends Element
{
    protected function __init()
    {
        $this->tag('i')->css('icon-home');
    }
}

class ExampleBuildElement extends Element
{
    protected function __build()
    {
        $this->tag('span');
        $this->add('__build');
    }
}

class PasswordInput extends Element
{
    protected $attributes = [
        'type' => 'password'
    ];
}