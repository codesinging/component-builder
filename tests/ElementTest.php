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
    public function testTag()
    {
        self::assertEquals('<div></div>', new Element('div'));
        self::assertEquals('<i></i>', new Element('i'));
        self::assertEquals('<b></b>', (new Element('i'))->tag('b'));
    }

    public function testContent()
    {
        self::assertEquals('<div>a</div>', new Element('div', 'a'));
        self::assertEquals('<div>ab</div>', (new Element('div', 'a'))->add('b'));
        self::assertEquals('<div>ba</div>', (new Element('div', 'a'))->prepend('b'));
        self::assertEquals('<div>ab</div>', (new Element('div', 'a'))->content('b'));
        self::assertEquals('<div>ab</div>', (new Element('div'))->add('a', 'b'));
        self::assertEquals('<div>abc</div>', (new Element('div', 'c'))->prepend('a', 'b'));
    }

    public function testEmpty()
    {
        self::assertTrue((new Element())->empty());
        self::assertFalse((new Element())->content('ab')->empty());
    }

    public function testSet()
    {
        self::assertEquals('<div id="app"></div>', new Element('div', null, ['id' => 'app']));
        self::assertEquals('<div id="app"></div>', (new Element())->set('id', 'app'));
        self::assertEquals('<div id="app"></div>', (new Element())->set(['id' => 'app']));
        self::assertEquals('<div id="app" data-id="1"></div>', new Element('div', null, ['id' => 'app', 'data-id' => 1]));
        self::assertEquals('<div id="app" data-id="1"></div>', (new Element())->set(['id' => 'app', 'data-id' => 1]));
    }

    public function testSetBind()
    {
        self::assertEquals('<div :id="id"></div>', (new Element())->set('id', 'app', 'id'));
        self::assertEquals('<div :id="id"></div>', (new Element())->set(['id' => 'app'], ['id' => 'id']));
        self::assertEquals('<div :id="id" pid="pid"></div>', (new Element())->set(['id' => 'app', 'pid' => 'pid'], ['id' => 'id']));
        self::assertEquals('<div :id="id" :pid="parent.id"></div>', (new Element())->set(['id' => 'app', 'pid' => 'pid'], ['id' => 'id', 'pid' => 'parent.id']));
        self::assertEquals('<div :id="stores.id"></div>', (new Element())->set('id', 'app', 'stores.id'));
    }

    public function testSetBindStore()
    {
        self::assertEquals('<div :id="id"></div>', (new Element())->set('id', 11, 'id', true));
        self::assertEquals('<div :bid="book.id"></div>', (new Element())->set('bid', '99', 'book.id', true));
        self::assertEquals('<div :cid="cid"></div>', (new Element())->set(['cid' => 22], ['cid' => 'cid'], ['cid']));
        self::assertEquals('<div :qid="qid" pid="44"></div>', (new Element())->set(['qid' => '33', 'pid' => '44'], ['qid' => 'qid'], ['pid']));
        self::assertEquals('<div :uid="uid" :vid="v.id"></div>', (new Element())->set(['uid' => '55', 'vid' => '66'], ['uid' => 'uid', 'vid' => 'v.id'], ['vid']));

        self::assertEquals(11, Store::get('id'));
        self::assertEquals(99, Store::get('book.id'));
        self::assertEquals(22, Store::get('cid'));
        self::assertEquals(66, Store::get('v.id'));
    }

    public function testBind()
    {
        self::assertEquals('<div :id="id"></div>', (new Element())->bind('id', 'id'));
        self::assertEquals('<div :id="id"></div>', (new Element())->bind(['id' => 'id']));
    }

    public function testGet()
    {
        self::assertEquals(100, (new Element('div'))->set('id', 100)->get('id'));
    }

    public function testEnd()
    {
        self::assertEquals('<div></div>', new Element('div'));
        self::assertEquals('<input>', (new Element('input', '', null, false)));
    }

    public function testGlue()
    {
        self::assertEquals('<div>a' . PHP_EOL . 'b</div>', (new Element())->add('a', 'b')->glue());
    }

    public function testEol()
    {
        self::assertEquals('<div>a</div>', new Element('div', 'a'));
        self::assertEquals('<div>' . PHP_EOL . 'a' . PHP_EOL . '</div>', new Element('div', 'a', null, true, true));
        self::assertEquals('<div>' . PHP_EOL . '</div>', new Element('div', '', null, true, true));
    }

    public function testCss()
    {
        self::assertEquals('<div class="margin"></div>', (new Element())->css('margin')->build());
        self::assertEquals('<div class="margin"></div>', (new Element())->css('margin'));
        self::assertEquals('<div class="margin padding"></div>', (new Element())->set('class', 'margin')->css('padding')->build());
    }

    public function testStyle()
    {
        self::assertEquals('<div style="color:white;"></div>', (new Element())->style('color:white'));
    }

    public function testParent()
    {
        self::assertEquals('<div><i>inner</i></div>', (new Element('i'))->parent('div')->content('inner')->build());
        self::assertEquals('<div class="outer"><i>inner</i></div>', (new Element('i'))->content('inner')->parent(function (Element $parent) {
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
        self::assertEquals('<button @click="click"></button>', (new Element('button'))->vOn('click')->build());
        self::assertEquals('<button @on-change="onChange"></button>', (new Element('button'))->vOn('on-change')->build());
        self::assertEquals('<button @click="click" @on-change="onChange"></button>', (new Element('button'))->vOn(['click' => 'click', 'on-change' => 'onChange']));
        self::assertEquals('<button @click="click" @on-change="onChange"></button>', (new Element('button'))->vOn(['click', 'on-change']));
    }

    public function testInit()
    {
        self::assertEquals('<i class="icon-home"></i>', new ExampleInitElement());
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
        $this->content('__build');
    }
}