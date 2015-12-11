<?php
/**
 * @link https://github.com/paulzi/yii2-auto-tree
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-auto-tree/blob/master/LICENSE)
 */

namespace paulzi\autotree\tests\models;

use paulzi\adjacencyList\AdjacencyListBehavior;
use paulzi\nestedsets\NestedSetsBehavior;
use paulzi\autotree\AutoTreeTrait;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $sort
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $path
 * @property string $slug
 *
 * @property NodeAlNs[] $parents
 * @property NodeAlNs $parent
 * @property NodeAlNs $root
 * @property NodeAlNs[] $descendants
 * @property NodeAlNs[] $children
 * @property NodeAlNs[] $leaves
 * @property NodeAlNs $prev
 * @property NodeAlNs $next
 *
 * @method static NodeAlNs|null findOne() findOne($condition)
 *
 * @mixin AdjacencyListBehavior
 * @mixin NestedSetsBehavior
 */
class NodeAlNs extends \yii\db\ActiveRecord
{
    use AutoTreeTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tree}}';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'         => AdjacencyListBehavior::className(),
                'sortable'      => false,
            ],
            [
                'class'         => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @return NodeQuery
     */
    public static function find()
    {
        return new NodeQuery(get_called_class());
    }
}