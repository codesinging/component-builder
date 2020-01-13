<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2020/1/13 10:00
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Builder;
use PHPUnit\Framework\TestCase;

class DirectiveTest extends TestCase
{

    public function testVText()
    {
        self::assertEquals('<div v-text="msg"></div>', (new Builder())->vText('msg'));
    }

    public function testVHtml()
    {
        self::assertEquals('<div v-html="html"></div>', (new Builder())->vHtml('html'));
    }

    public function testVShow()
    {
        self::assertEquals('<div v-show="show"></div>', (new Builder())->vShow('show'));
    }

    public function testVIf()
    {
        self::assertEquals('<div v-if="condition"></div>', (new Builder())->vIf('condition'));
    }

    public function testVElse()
    {
        self::assertEquals('<div v-else></div>', (new Builder())->vElse());
    }

    public function testVElseIf()
    {
        self::assertEquals('<div v-else-if="condition"></div>', (new Builder())->vElseIf('condition'));
    }

    public function testVFor()
    {
        self::assertEquals('<div v-for="item in items"></div>', (new Builder())->vFor('item in items'));
    }

    public function testVOn()
    {
        self::assertEquals('<button @click="onClick"></button>', (new Builder('button'))->vOn('click', 'onClick'));
        self::assertEquals('<button @click="click"></button>', (new Builder('button'))->vOn('click'));
        self::assertEquals('<button @on-change="onChange"></button>', (new Builder('button'))->vOn('on-change'));
        self::assertEquals('<button @click="click" @on-change="onChange"></button>', (new Builder('button'))->vOn(['click' => 'click', 'on-change' => 'onChange']));
        self::assertEquals('<button @click="click" @on-change="onChange"></button>', (new Builder('button'))->vOn(['click', 'on-change']));
    }

    public function testVClick()
    {
        self::assertEquals('<button @click="onClick"></button>', (new Builder('button'))->vClick('onClick'));
        self::assertEquals('<button @click.stop="onClick"></button>', (new Builder('button'))->vClick('onClick', 'stop'));
    }

    public function testVClickBind()
    {
        self::assertEquals('<button @click="message = \'hello world\'"></button>', (new Builder('button'))->vClickBind('message', 'hello world'));
        self::assertEquals('<button @click="age = 20"></button>', (new Builder('button'))->vClickBind('age', 20));
        self::assertEquals('<button @click="visible = true"></button>', (new Builder('button'))->vClickBind('visible', true));
        self::assertEquals('<button @click="visible = false"></button>', (new Builder('button'))->vClickBind('visible', false));
    }

    public function testVModel()
    {
        self::assertEquals('<input v-model="name">', (new Builder('input'))->closing(false)->vModel('name'));
        self::assertEquals('<input v-model.number="age">', (new Builder('input'))->closing(false)->vModel('age', 'number'));
    }

    public function testRef()
    {
        self::assertEquals('<input ref="name">', (new Builder('input'))->closing(false)->ref('name'));
    }
}