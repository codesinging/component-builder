<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:21
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Builder;
use CodeSinging\ComponentBuilder\Store;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{
    public function testInstance()
    {
        self::assertInstanceOf(Builder::class, Builder::instance());
    }

    public function testTag()
    {
        self::assertEquals('<div></div>', new Builder('div'));
        self::assertEquals('<i></i>', new Builder('i'));
        self::assertEquals('<b></b>', (new Builder('i'))->tag('b'));
    }

    public function testSet()
    {
        self::assertEquals('<div id="app"></div>', new Builder('div', null, ['id' => 'app']));
        self::assertEquals('<div id="app"></div>', (new Builder())->set('id', 'app'));
        self::assertEquals('<div id="app"></div>', (new Builder())->set(['id' => 'app']));
        self::assertEquals('<div id="app" :data-id="1"></div>', new Builder('div', null, ['id' => 'app', 'data-id' => 1]));
        self::assertEquals('<div id="app" :data-id="1"></div>', (new Builder())->set(['id' => 'app', 'data-id' => 1]));
    }

    public function testSetAndBind()
    {
        self::assertEquals('<div :id="id"></div>', (new Builder())->set('id', ':id'));
        self::assertEquals('<div :id="id"></div>', (new Builder())->set(['id' => ':id']));
        self::assertEquals('<div :id="id" pid="pid"></div>', (new Builder())->set(['id' => ':id', 'pid' => 'pid']));
        self::assertEquals('<div :id="id" :pid="parent.id"></div>', (new Builder())->set(['id' => ':id', 'pid' => ':parent.id']));
        self::assertEquals('<div :id="stores.id"></div>', (new Builder())->set('id', ':stores.id'));
    }

    public function testSetAndBindAndStore()
    {
        self::assertEquals('<div :id="id"></div>', (new Builder())->set('id',  'id', 11));
        self::assertEquals('<div :bid="book.id"></div>', (new Builder())->set('bid',  'book.id', 99));
        self::assertEquals('<div :cid="cid"></div>', (new Builder())->set(['cid' => 'cid'], ['cid' => 22]));
        self::assertEquals('<div :uid="uid" :vid="v.id"></div>', (new Builder())->set(['uid' => ':uid', 'vid' => 'v.id'], ['vid' => 66]));

        self::assertEquals(11, Store::get('id'));
        self::assertEquals(99, Store::get('book.id'));
        self::assertEquals(22, Store::get('cid'));
        self::assertEquals(66, Store::get('v.id'));
    }

    public function testGet()
    {
        self::assertEquals(100, (new Builder('div'))->set('id', 100)->get('id'));
    }

    public function testClosing()
    {
        self::assertEquals('<div></div>', new Builder('div'));
        self::assertEquals('<input>', (new Builder('input', '', null, false)));
        self::assertEquals('<input>', (new Builder('input'))->closing(false));
    }

    public function testLineBreak()
    {
        self::assertEquals('<div>a</div>', new Builder('div', 'a'));
        self::assertEquals('<div>' . PHP_EOL . 'a' . PHP_EOL . '</div>', new Builder('div', 'a', null, true, true));
        self::assertEquals('<div>' . PHP_EOL . '</div>', (string)(new Builder('div', null, null, true, true)));
    }

    public function testCss()
    {
        self::assertEquals('<div class="margin"></div>', (new Builder())->css('margin'));
        self::assertEquals('<div class="margin"></div>', (new Builder())->css('margin'));
        self::assertEquals('<div class="margin padding"></div>', (new Builder())->set('class', 'margin')->css('padding'));
    }

    public function testStyle()
    {
        self::assertEquals('<div style="color:white;"></div>', (new Builder())->style('color:white'));
    }

    public function testAdd()
    {
        self::assertEquals('<div>ab</div>', (new Builder('div', 'a'))->add('b'));
        self::assertEquals('<div>ab</div>', (new Builder('div'))->add('a', 'b'));
    }

    public function testPrepend()
    {
        self::assertEquals('<div>ba</div>', (new Builder('div', 'a'))->prepend('b'));
        self::assertEquals('<div>abc</div>', (new Builder('div', 'c'))->prepend('a', 'b'));
    }

    public function testInterpolation()
    {
        self::assertEquals('<div>{{ name }}</div>', (new Builder('div'))->interpolation('name'));
        self::assertEquals('<div>{{ age }}</div>', (new Builder('div'))->interpolation('age', 20));
        self::assertEquals(20, Store::get('age'));
    }

    public function testSlot()
    {
        self::assertEquals('<div><template slot="header">Header</template></div>', (new Builder())->slot('header', 'Header'));
    }

    public function testEmpty()
    {
        self::assertTrue((new Builder())->isEmpty());
        self::assertFalse((new Builder())->add('ab')->isEmpty());
    }

    public function testGlue()
    {
        self::assertEquals('<div>a' . PHP_EOL . 'b</div>', (new Builder())->add('a', 'b')->glue());
    }

    public function testParent()
    {
        self::assertEquals('<div><i>inner</i></div>', (string)(new Builder('i'))->parent('div')->add('inner'));
        self::assertEquals('<div class="outer"><i>inner</i></div>', (new Builder('i'))->add('inner')->parent(function (Builder $parent) {
            $parent->css('outer');
        }));
    }

    public function testCall()
    {
        self::assertEquals('<div size="small" :plain="true" :round="false" :native-type="nativeType"></div>', (new DemoBuilder())->size('small')->plain()->round(false)->nativeType('nativeType', 'submit'));
        self::assertEquals('submit', Store::get('nativeType'));
    }

    public function testInit()
    {
        self::assertEquals('<i class="icon-home"></i>', new ExampleInitElement());
    }

    public function testAttributes()
    {
        self::assertEquals('<input type="password">', (new Builder('input', null, ['type' => 'password'], false)));
        self::assertEquals('<input type="password">', new PasswordInput('input', null, [], false));
    }

    public function testBuild()
    {
        self::assertEquals('<span>__build</span>', new ExampleBuildElement('div'));
    }
}

class ExampleInitElement extends Builder
{
    protected function __init()
    {
        $this->tag('i')->css('icon-home');
    }
}

class ExampleBuildElement extends Builder
{
    protected function __build()
    {
        $this->tag('span');
        $this->add('__build');
    }
}

class PasswordInput extends Builder
{
    protected $attributes = [
        'type' => 'password'
    ];
}

/**
 * Class DemoComponent
 *
 * @method $this size(string $size, $store = null)
 * @method $this plain(bool $plain = true, $store = null)
 * @method $this round(bool $round = true, $store = null)
 * @method $this nativeType(string $nativeType, $store = null)
 *
 * @package CodeSinging\ComponentBuilder\Tests
 */
class DemoBuilder extends Builder
{

}