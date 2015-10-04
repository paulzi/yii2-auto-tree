<?php
/**
 * @link https://github.com/paulzi/yii2-auto-tree
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-auto-tree/blob/master/LICENSE)
 */

namespace paulzi\autotree\tests;

use paulzi\autotree\tests\models\NodeAlNs;
use Yii;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 * @group AlNs
 */
class AlNsTest extends AutoTreeTraitTestCase
{
    public function getModelClass()
    {
        return NodeAlNs::className();
    }

    public function testMakeRootInsert()
    {
        $node = new NodeAlNs(['slug' => 'r']);
        $this->assertTrue($node->makeRoot()->save());

        $node->refresh();
        $this->assertEquals(null, $node->parent_id);
        $this->assertEquals(1,    $node->lft);
        $this->assertEquals(2,    $node->rgt);
        $this->assertEquals(0,    $node->depth);
    }

    public function testMakeRootUpdate()
    {
        $node = NodeAlNs::findOne(9);
        $this->assertTrue($node->makeRoot()->save());

        $node->refresh();
        $this->assertEquals(null, $node->parent_id);
        $this->assertEquals(1,    $node->lft);
        $this->assertEquals(8,    $node->rgt);
        $this->assertEquals(0,    $node->depth);
    }

    public function testPrependTo()
    {
        $node = new NodeAlNs(['slug' => 'new']);
        $this->assertTrue($node->prependTo(NodeAlNs::findOne(1))->save());

        $node->refresh();
        $this->assertEquals(1, $node->parent_id);
        $this->assertEquals(2, $node->lft);
        $this->assertEquals(3, $node->rgt);
        $this->assertEquals(1, $node->depth);
    }

    public function testPrependToAnotherTree()
    {
        $node = NodeAlNs::findOne(30);
        $this->assertTrue($node->prependTo(NodeAlNs::findOne(4))->save());

        $node->refresh();
        $this->assertEquals(4, $node->parent_id);
        $this->assertEquals(31, $node->lft);
        $this->assertEquals(40, $node->rgt);
        $this->assertEquals(2, $node->depth);
    }

    public function testAppendTo()
    {
        $node = NodeAlNs::findOne(10);
        $this->assertTrue($node->appendTo(NodeAlNs::findOne(18))->save());

        $node->refresh();
        $this->assertEquals(18, $node->parent_id);
        $this->assertEquals(23, $node->lft);
        $this->assertEquals(30, $node->rgt);
        $this->assertEquals(4, $node->depth);
    }

    public function testInsertBefore()
    {
        $node = new NodeAlNs(['slug' => 'new']);
        $this->assertTrue($node->insertBefore(NodeAlNs::findOne(22))->save());

        $node->refresh();
        $this->assertEquals(9, $node->parent_id);
        $this->assertEquals(38, $node->lft);
        $this->assertEquals(39, $node->rgt);
        $this->assertEquals(3, $node->depth);
    }

    public function testInsertAfter()
    {
        $node = NodeAlNs::findOne(32);
        $this->assertTrue($node->insertAfter(NodeAlNs::findOne(30))->save());

        $node->refresh();
        $this->assertEquals(26, $node->parent_id);
        $this->assertEquals(26, $node->lft);
        $this->assertEquals(27, $node->rgt);
        $this->assertEquals(1, $node->depth);
    }

    public function testInsertAfterAnotherTree()
    {
        $node = NodeAlNs::findOne(26);
        $this->assertTrue($node->insertAfter(NodeAlNs::findOne(21))->save());

        $node->refresh();
        $this->assertEquals(9, $node->parent_id);
        $this->assertEquals(38, $node->lft);
        $this->assertEquals(65, $node->rgt);
        $this->assertEquals(3, $node->depth);
    }

    public function testDelete()
    {
        $this->assertEquals(1, NodeAlNs::findOne(30)->delete());
        $this->assertEquals(null, NodeAlNs::findOne(30));
    }

    public function testDeleteWithChildren()
    {
        $this->assertEquals(10, NodeAlNs::findOne(4)->deleteWithChildren());
        $this->assertEquals(null, NodeAlNs::findOne(24));
    }
}