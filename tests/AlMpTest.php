<?php
/**
 * @link https://github.com/paulzi/yii2-auto-tree
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-auto-tree/blob/master/LICENSE)
 */

namespace paulzi\autotree\tests;

use paulzi\autotree\tests\models\NodeAlMp;
use Yii;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 * @group AlMp
 */
class AlMpTest extends AutoTreeTraitTestCase
{
    public function getModelClass()
    {
        return NodeAlMp::className();
    }

    public function testMakeRootInsert()
    {
        $node = new NodeAlMp(['slug' => 'r']);
        $this->assertTrue($node->makeRoot()->save());

        $node->refresh();
        $this->assertEquals(null, $node->parent_id);
        $this->assertEquals(0,    $node->sort);
        $this->assertEquals(0,    $node->depth);
    }

    public function testMakeRootUpdate()
    {
        $node = NodeAlMp::findOne(9);
        $this->assertTrue($node->makeRoot()->save());

        $node->refresh();
        $this->assertEquals(null, $node->parent_id);
        $this->assertEquals(0,    $node->sort);
        $this->assertEquals(0,    $node->depth);
    }

    public function testPrependTo()
    {
        $node = new NodeAlMp(['slug' => 'new']);
        $this->assertTrue($node->prependTo(NodeAlMp::findOne(1))->save());

        $node->refresh();
        $this->assertEquals(1,    $node->parent_id);
        $this->assertEquals(-101, $node->sort);
        $this->assertEquals(1,    $node->depth);
    }

    public function testPrependToAnotherTree()
    {
        $node = NodeAlMp::findOne(30);
        $this->assertTrue($node->prependTo(NodeAlMp::findOne(4))->save());

        $node->refresh();
        $this->assertEquals(4,    $node->parent_id);
        $this->assertEquals(-100, $node->sort);
        $this->assertEquals(2,    $node->depth);
    }

    public function testAppendTo()
    {
        $node = NodeAlMp::findOne(10);
        $this->assertTrue($node->appendTo(NodeAlMp::findOne(18))->save());

        $node->refresh();
        $this->assertEquals(18, $node->parent_id);
        $this->assertEquals(0, $node->sort);
        $this->assertEquals(4, $node->depth);
    }

    public function testInsertBefore()
    {
        $node = new NodeAlMp(['slug' => 'new']);
        $this->assertTrue($node->insertBefore(NodeAlMp::findOne(22))->save());

        $node->refresh();
        $this->assertEquals(9, $node->parent_id);
        $this->assertEquals(3, $node->sort);
        $this->assertEquals(3, $node->depth);
    }

    public function testInsertAfter()
    {
        $node = NodeAlMp::findOne(32);
        $this->assertTrue($node->insertAfter(NodeAlMp::findOne(30))->save());

        $node->refresh();
        $this->assertEquals(26, $node->parent_id);
        $this->assertEquals(3, $node->sort);
        $this->assertEquals(1, $node->depth);
    }

    public function testInsertAfterAnotherTree()
    {
        $node = NodeAlMp::findOne(26);
        $this->assertTrue($node->insertAfter(NodeAlMp::findOne(21))->save());

        $node->refresh();
        $this->assertEquals(9, $node->parent_id);
        $this->assertEquals(3, $node->sort);
        $this->assertEquals(3, $node->depth);
    }

    public function testDelete()
    {
        $this->assertEquals(1, NodeAlMp::findOne(30)->delete());
        $this->assertEquals(null, NodeAlMp::findOne(30));
    }

    public function testDeleteWithChildren()
    {
        $this->assertEquals(10, NodeAlMp::findOne(4)->deleteWithChildren());
        $this->assertEquals(null, NodeAlMp::findOne(24));
        $this->assertEquals(15, NodeAlMp::findOne(1)->deleteWithChildren());
    }
}