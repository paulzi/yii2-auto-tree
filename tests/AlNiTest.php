<?php
/**
 * @link https://github.com/paulzi/yii2-auto-tree
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-auto-tree/blob/master/LICENSE)
 */

namespace paulzi\autotree\tests;

use paulzi\autotree\tests\models\NodeAlNi;
use Yii;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 * @group AlNi
 */
class AlNiTest extends AutoTreeTraitTestCase
{
    /**
     * @inheritdoc
     */
    public function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet(require(__DIR__ . '/data/data-ni.php'));
    }

    public function getModelClass()
    {
        return NodeAlNi::className();
    }

    public function testMakeRootInsert()
    {
        $node = new NodeAlNi(['slug' => 'r']);
        $this->assertTrue($node->makeRoot()->save());

        $node->refresh();
        $this->assertEquals(null,       $node->parent_id);
        $this->assertEquals(0,          $node->lft);
        $this->assertEquals(2147483647, $node->rgt);
        $this->assertEquals(0,          $node->depth);
    }

    public function testMakeRootUpdate()
    {
        $node = NodeAlNi::findOne(9);
        $this->assertTrue($node->makeRoot()->save());

        $node->refresh();
        $this->assertEquals(null,       $node->parent_id);
        $this->assertEquals(0,          $node->lft);
        $this->assertEquals(2147483647, $node->rgt);
        $this->assertEquals(0,          $node->depth);
    }

    public function testPrependTo()
    {
        $node = new NodeAlNi(['slug' => 'new']);
        $this->assertTrue($node->prependTo(NodeAlNi::findOne(1))->save());

        $node->refresh();
        $this->assertEquals(1, $node->parent_id);
        $this->assertEquals(1, $node->lft);
        $this->assertEquals(2, $node->rgt);
        $this->assertEquals(1, $node->depth);
    }

    public function testPrependToAnotherTree()
    {
        $node = NodeAlNi::findOne(30);
        $this->assertTrue($node->prependTo(NodeAlNi::findOne(4))->save());

        $node->refresh();
        $this->assertEquals(4, $node->parent_id);
        $this->assertEquals(1091940810, $node->lft);
        $this->assertEquals(1419523107, $node->rgt);
        $this->assertEquals(2, $node->depth);
    }

    public function testAppendTo()
    {
        $node = NodeAlNi::findOne(10);
        $this->assertTrue($node->appendTo(NodeAlNi::findOne(18))->save());

        $node->refresh();
        $this->assertEquals(18, $node->parent_id);
        $this->assertEquals(1000013, $node->lft);
        $this->assertEquals(1000024, $node->rgt);
        $this->assertEquals(4, $node->depth);
    }

    public function testInsertBefore()
    {
        $node = new NodeAlNi(['slug' => 'new']);
        $this->assertTrue($node->insertBefore(NodeAlNi::findOne(22))->save());

        $node->refresh();
        $this->assertEquals(9, $node->parent_id);
        $this->assertEquals(300000005, $node->lft);
        $this->assertEquals(300000006, $node->rgt);
        $this->assertEquals(3, $node->depth);
    }

    public function testInsertAfter()
    {
        $node = NodeAlNi::findOne(32);
        $this->assertTrue($node->insertAfter(NodeAlNi::findOne(30))->save());

        $node->refresh();
        $this->assertEquals(26, $node->parent_id);
        $this->assertEquals(2147483644, $node->lft);
        $this->assertEquals(2147483646, $node->rgt);
        $this->assertEquals(1, $node->depth);
    }

    public function testInsertAfterAnotherTree()
    {
        $node = NodeAlNi::findOne(26);
        $this->assertTrue($node->insertAfter(NodeAlNi::findOne(21))->save());

        $node->refresh();
        $this->assertEquals(9, $node->parent_id);
        $this->assertEquals(1031907726, $node->lft);
        $this->assertEquals(1784921473, $node->rgt);
        $this->assertEquals(3, $node->depth);
    }

    public function testDelete()
    {
        $this->assertEquals(1, NodeAlNi::findOne(30)->delete());
        $this->assertEquals(null, NodeAlNi::findOne(30));
    }

    public function testDeleteWithChildren()
    {
        $this->assertEquals(10, NodeAlNi::findOne(4)->deleteWithChildren());
        $this->assertEquals(null, NodeAlNi::findOne(24));
        $this->assertEquals(15, NodeAlNi::findOne(1)->deleteWithChildren());
    }
}