<?php
/**
 * @link https://github.com/paulzi/yii2-auto-tree
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-auto-tree/blob/master/LICENSE)
 */

namespace paulzi\autotree\tests;

use Yii;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 */
class AutoTreeTraitTestCase extends BaseTestCase
{
    /**
     * @return \yii\db\BaseActiveRecord
     */
    public function getModelClass()
    {
        return null;
    }
    
    public function testGetParents()
    {
        $modelClass = $this->getModelClass();
        
        $data = [1, 4, 9];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, $modelClass::findOne(21)->parents));

        $data = [];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, $modelClass::findOne(1)->parents));

        $data = [2, 7];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, $modelClass::findOne(17)->getParents(2)->all()));

        $data = [26, 30];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, $modelClass::findOne(38)->parents));
    }

    public function testGetParent()
    {
        $modelClass = $this->getModelClass();
        $this->assertEquals(5, $modelClass::findOne(12)->parent->id);
        $this->assertEquals(26, $modelClass::findOne(29)->getParent()->one()->getAttribute('id'));
        $this->assertEquals(null, $modelClass::findOne(1)->parent);
    }

    public function testGetRoot()
    {
        $modelClass = $this->getModelClass();
        $this->assertEquals(26, $modelClass::findOne(28)->root->id);
        $this->assertEquals(26, $modelClass::findOne(26)->getRoot()->one()->getAttribute('id'));
    }

    public function testGetDescendants()
    {
        $modelClass = $this->getModelClass();

        $data  = [8, 9, 10, 20, 21, 22, 23, 24, 25];
        $data2 = array_map(function ($value) { return $value->id; }, $modelClass::findOne(4)->descendants);
        sort($data2);
        $this->assertEquals($data, $data2);

        $data  = [2, 5, 6, 7];
        $data2 = array_map(function ($value) { return $value->id; }, $modelClass::findOne(2)->getDescendants(1, true)->all());
        sort($data2);
        $this->assertEquals($data, $data2);

        $data = [];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, $modelClass::findOne(8)->descendants));
    }

    public function testGetChildren()
    {
        $modelClass = $this->getModelClass();

        $data = [8, 9, 10];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, $modelClass::findOne(4)->children));

        $data = [];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, $modelClass::findOne(28)->getChildren()->all()));
    }

    public function testGetLeaves()
    {
        $modelClass = $this->getModelClass();

        $data = [8, 20, 21, 22, 23, 24, 25];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, $modelClass::findOne(4)->leaves));

        $data = [3, 8];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, $modelClass::findOne(1)->getLeaves(2)->all()));
    }

    public function testGetPrev()
    {
        $modelClass = $this->getModelClass();
        $this->assertEquals(11, $modelClass::findOne(12)->prev->id);
        $this->assertEquals(null, $modelClass::findOne(20)->getPrev()->one());
    }

    public function testGetNext()
    {
        $modelClass = $this->getModelClass();
        $this->assertEquals(13, $modelClass::findOne(12)->next->id);
        $this->assertEquals(null, $modelClass::findOne(19)->getNext()->one());
    }

    public function testIsRoot()
    {
        $modelClass = $this->getModelClass();
        $this->assertTrue($modelClass::findOne(1)->isRoot());
        $this->assertTrue($modelClass::findOne(26)->isRoot());
        $this->assertFalse($modelClass::findOne(3)->isRoot());
        $this->assertFalse($modelClass::findOne(37)->isRoot());
    }

    public function testIsChildOf()
    {
        $modelClass = $this->getModelClass();
        $this->assertTrue($modelClass::findOne(10)->isChildOf($modelClass::findOne(1)));
        $this->assertTrue($modelClass::findOne(9)->isChildOf($modelClass::findOne(4)));
        $this->assertFalse($modelClass::findOne(12)->isChildOf($modelClass::findOne(15)));
        $this->assertFalse($modelClass::findOne(21)->isChildOf($modelClass::findOne(22)));
        $this->assertFalse($modelClass::findOne(8)->isChildOf($modelClass::findOne(8)));
        $this->assertFalse($modelClass::findOne(6)->isChildOf($modelClass::findOne(27)));
    }

    public function testIsLeaf()
    {
        $modelClass = $this->getModelClass();
        $this->assertTrue($modelClass::findOne(3)->isLeaf());
        $this->assertFalse($modelClass::findOne(4)->isLeaf());
    }
}