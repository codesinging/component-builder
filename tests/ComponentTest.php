<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:27
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Component;
use CodeSinging\ComponentBuilder\Store;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{
    public function testConstruct()
    {
        self::assertEquals('<component size="small" plain></component>', new Component(['size' => 'small', 'plain']));
    }

    public function testBaseTag()
    {
        self::assertEquals('example-component', (new ExampleComponent())->baseTag());
        self::assertEquals('example-tag', (new ExampleTagComponent())->baseTag());
    }

    public function testBuild()
    {
        self::assertEquals('<component></component>', new Component());
        self::assertEquals('<example-component></example-component>', new ExampleComponent());
        self::assertEquals('<el-exam-component></el-exam-component>', new ExamComponent());
    }

    public function testCall()
    {
        self::assertEquals('<demo-component size="small" :plain="true" :round="false" :native-type="nativeType"></demo-component>', (new DemoComponent())->size('small')->plain()->round(false)->nativeType('nativeType', 'submit'));
        self::assertEquals('submit', Store::get('nativeType'));
    }
}

class ExampleComponent extends Component
{

}

class ExamComponent extends Component
{
    protected $tagPrefix = 'el-';
}

class ExampleTagComponent extends Component
{
    protected $baseTag = 'example-tag';
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
class DemoComponent extends Component
{

}