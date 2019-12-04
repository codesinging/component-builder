<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 18:27
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Component;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{
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