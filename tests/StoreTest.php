<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 17:24
 */

namespace CodeSinging\ComponentBuilder\Tests;

use CodeSinging\ComponentBuilder\Store;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    public function testSetGetAll()
    {
        Store::set('id', 1);
        Store::set('parent.id', 2);
        self::assertEquals(1, Store::get('id'));
        self::assertEquals(2, Store::get('parent.id'));
        self::assertEquals(2, Store::all()['parent']['id']);
        self::assertEquals(3, Store::get('cid', 3));
    }
}